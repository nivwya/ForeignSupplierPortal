<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportsController extends Controller
{
    public function reportsContent(Request $request)
    {
        // PO Acknowledgement
        $poStatuses = DB::table('purchase_orders')
        ->select('ack_status')
        ->distinct()
        ->whereNotNull('ack_status')
        ->pluck('ack_status')
        ->toArray();
    
    $poQuery = PurchaseOrder::query();
    if ($request->filled('ack_po_number')) {
        $poQuery->where('order_number', $request->ack_po_number);
    }
    if ($request->filled('ack_status')) {
        $poQuery->where('ack_status', $request->ack_status);
    }
    
    $poReports = $poQuery->orderBy('order_date', 'desc')->get();
    

        // Delivery Performance
$deliveryQuery = DB::table('delivery_items')
    ->join('deliveries', 'deliveries.id', '=', 'delivery_items.delivery_id')
    ->join('purchase_orders', 'purchase_orders.id', '=', 'deliveries.order_id')
    ->join('purchase_order_items', 'purchase_order_items.id', '=', 'delivery_items.purchase_order_item_id')
    ->select(
        'purchase_orders.order_number as po_number',
        'purchase_order_items.product_code as material_code',
        'delivery_items.item_description',
        'delivery_items.quantity as ordered_qty',
        'delivery_items.quantity_supplied as delivered_qty',
        'delivery_items.expected_delv_date as promised_date',
        'delivery_items.supply_date as actual_date'
    );
if ($request->filled('delivery_po_number')) {
    $deliveryQuery->where('purchase_orders.order_number', $request->delivery_po_number);
}
$deliveryResults = $deliveryQuery->get();
$deliveryReports = [];
foreach ($deliveryResults as $row) {
    $diffLabel = null;
    $status = null;
    if ($row->actual_date && $row->promised_date) {
        $actual = Carbon::parse($row->actual_date);
        $promised = Carbon::parse($row->promised_date);
        $days = $actual->diffInDays($promised);
        if ($actual->lt($promised)) {
            $diffLabel = "{$days} days early";
            $status = 'Early';
        } elseif ($actual->gt($promised)) {
            $diffLabel = "{$days} days delayed";
            $status = 'Delayed';
        } else {
            $diffLabel = "On time";
            $status = 'On Time';
        }
    }
    if ($request->filled('delivery_status') && $request->delivery_status !== $status) {
        continue;
    }
    $deliveryReports[] = (object)[
        'po_number'            => $row->po_number,
        'material_code'        => $row->material_code,
        'material_description' => $row->item_description,
        'ordered_qty'          => $row->ordered_qty,
        'delivered_qty'        => $row->delivered_qty,
        'promised_date'        => $row->promised_date,
        'actual_date'          => $row->actual_date,
        'days_diff'            => $diffLabel,
    ];
}


// GRN Report
$grnQuery = DB::table('deliveries')
->join('delivery_items', 'deliveries.id', '=', 'delivery_items.delivery_id')
->select(
    'deliveries.order_id',
    'deliveries.grn_num',
    'deliveries.grn_date',
    'delivery_items.line_item_num as line_item',
    'delivery_items.quantity_supplied',
    'delivery_items.qty_received_by_amg',
    'delivery_items.remarks as remarks',
    'delivery_items.storage_location'
);

if ($request->filled('grn_line_no')) {
$grnQuery->where('delivery_items.line_item_num', $request->grn_line_no);
}
if ($request->filled('grn_po_number')) {
$grnQuery->where('deliveries.order_id', $request->grn_po_number);
}
$grnReports = $grnQuery->orderBy('deliveries.grn_date', 'desc')->get();

$grnReports->transform(function ($item) {
$supplied = (int) $item->quantity_supplied;
$received = (int) $item->qty_received_by_amg;

$diff = $supplied - $received;
$item->shortfall_excess = ($diff === 0) ? 'No' : 'Yes';

return $item;
});

// Invoice Status Report
$invoiceStatuses = DB::table('invoices')
    ->select('status')
    ->distinct()
    ->pluck('status')
    ->filter()
    ->values();

$invoiceQuery = DB::table('invoices')
    ->select(
        'invoices.invoice_number',
        'invoices.purchase_order_id', // Now directly selecting the PO ID
        'invoices.invoice_date',
        'invoices.amount as invoice_amount',
        'invoices.status as invoice_status',
        'invoices.miro_document',
        'invoices.rejection_reason',
        'invoices.due_date as expected_payment_date'
    );
if ($request->filled('invoice_po_number')) {
    $invoiceQuery->where('invoices.purchase_order_id', $request->invoice_po_number);
}
if ($request->filled('invoice_status')) {
    $invoiceQuery->where('invoices.status', $request->invoice_status);
}
$invoiceReports = $invoiceQuery->orderBy('invoices.invoice_date', 'desc')->get();


//payment 
$paymentQuery = DB::table('vendor_payments')
    ->select(
        'invoice_num',
        'payment_document_number',
        'payment_date',
        'amount',
        'reference_number',
        'status',
        'deductions',
        'balance_outstanding'
    );

if ($request->filled('payment_po_number')) {
    $paymentQuery->where('reference_number', $request->payment_po_number); // assuming reference_number links to PO
}

if ($request->filled('payment_status')) {
    $paymentQuery->where('status', $request->payment_status);
}

$paymentReports = $paymentQuery->orderBy('payment_date', 'desc')->get();

     // --- Vendor Performance Dashboard logic merged here ---

    $vendorQuery = PurchaseOrder::query();
    $totalPos = $vendorQuery->count();
    $acknowledgedOnTime = (clone $vendorQuery)
        ->where('status', 'Acknowledged')
        ->count();
    $acknowledgedPercent = $totalPos > 0 ? round(($acknowledgedOnTime / $totalPos) * 100, 2) : 0;
    $onTimeDeliveryPercent = 0;
    $quarterDelays = PurchaseOrder::selectRaw("
            CONCAT(YEAR(order_date), ' Q', QUARTER(order_date)) as quarter,
            COUNT(*) as total_orders,
            SUM(CASE
                WHEN delivery_date IS NOT NULL AND delivery_date > DATE_ADD(order_date, INTERVAL 30 DAY)
                THEN 1 ELSE 0
            END) as delayed_orders
        ")
        ->groupBy('quarter')
        ->orderBy('quarter')
        ->get();

    $delayChartLabels = $quarterDelays->pluck('quarter');
    $delayChartData = $quarterDelays->map(function ($row) {
        return $row->total_orders > 0
            ? round(($row->delayed_orders / $row->total_orders) * 100, 2)
            : 0;
    });

    // Backlog Report
    $backlogQuery = DB::table('purchase_order_items')
    ->join('purchase_orders', 'purchase_orders.id', '=', 'purchase_order_items.order_id')
    ->leftJoin('delivery_items', 'delivery_items.purchase_order_item_id', '=', 'purchase_order_items.id')
    ->select(
        'purchase_orders.order_number as po_number',
        'purchase_orders.order_date as order_date',
        'purchase_order_items.product_code as line_item',
        'delivery_items.item_description',
        'purchase_order_items.quantity as ordered_qty',
        'purchase_order_items.delivered_quantity as delivered_qty',
        'purchase_order_items.delivery_date',
        DB::raw('(purchase_order_items.quantity - purchase_order_items.delivered_quantity) as pending_qty'),
        'delivery_items.expected_delv_date as promised_date',
        'purchase_order_items.status as delivery_status'
    );

if ($request->filled('backlog_po_number')) {
    $backlogQuery->where('purchase_orders.order_number', $request->backlog_po_number);
}
if ($request->filled('backlog_line_item')) {
    $backlogQuery->where('purchase_order_items.product_code', $request->backlog_line_item);
}
$backlogReports = $backlogQuery->orderBy('purchase_order_items.delivery_date', 'asc')->get();

//returns
$returnStatuses = \App\Models\Returns::select('follow_up_status')
    ->distinct()
    ->whereNotNull('follow_up_status')
    ->pluck('follow_up_status')
    ->filter()
    ->values();
$query = \App\Models\Returns::query();
if ($request->filled('returns_po_number')) {
    $query->where('order_number', $request->returns_po_number);
}
if ($request->filled('returns_line_item')) {
    $query->where('line_item', $request->returns_line_item);
}
if ($request->filled('returns_status')) {
    $query->where('follow_up_status', $request->returns_status);
}
$returnsReports = $query->orderBy('return_date', 'desc')->get();


    // --- return all data to the same view ---
    return view('reports-content', compact(
        'poReports',
        'poStatuses',
        'deliveryReports',
        'grnReports',
        'invoiceReports',
        'invoiceStatuses',
        'totalPos',
        'acknowledgedPercent',
        'onTimeDeliveryPercent',
        'delayChartLabels',
        'delayChartData',
        'backlogReports',
        'returnsReports',
        'returnStatuses',
        'paymentReports'
    ));
}
}
