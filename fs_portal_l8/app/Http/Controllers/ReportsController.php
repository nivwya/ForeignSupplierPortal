<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\PoMaster;
use App\Models\PoDelivery;
use App\Models\GrnDelivery;
use App\Models\InvoiceData;
use App\Models\Vendor;

class ReportsController extends Controller
{
    public function reportsContent(Request $request)
    {
        // PO Acknowledgement
        $poStatuses = []; // No statuses to select
    
        $poQuery = PoMaster::query();
        if ($request->filled('ack_po_number')) {
            $poQuery->where('purchase_doc_no', $request->ack_po_number);
        }
        $poReports = $poQuery->orderBy('purchase_order_dt', 'desc')->get()->map(function($po) {
            // Try to fetch matching purchase_orders row for extra fields
            $legacy = \DB::table('purchase_orders')->where('order_number', $po->purchase_doc_no)->first();
            return (object) [
                'order_number' => $po->purchase_doc_no,
                'order_date' => $po->purchase_order_dt,
                'order_value' => $po->net_order ?? $po->gross_order ?? null,
                'ack_status' => $legacy->ack_status ?? null,
                'acknowledgement_date' => $legacy->acknowledgement_date ?? null,
                'status' => $legacy->status ?? null,
            ];
        });

        // Delivery Performance
        $deliveryQuery = PoDelivery::query()
            ->join('v002_pomaster', 'v002_pomaster.purchase_doc_no', '=', 'v003_podelivery.prchase_doc_number')
            ->select(
                'v002_pomaster.purchase_doc_no as po_number',
                'v003_podelivery.item_no as material_code',
                'v003_podelivery.ADD_TEXT1 as material_description',
                'v003_podelivery.schedule_qty as ordered_qty',
                'v003_podelivery.goods_qty as delivered_qty',
                'v003_podelivery.itm_delivery_dt as promised_date',
                'v003_podelivery.stat_del_dt as actual_date'
            );
        if ($request->filled('delivery_po_number')) {
            $deliveryQuery->where('v002_pomaster.purchase_doc_no', $request->delivery_po_number);
        }
        $deliveryResults = $deliveryQuery->get();
        $deliveryReports = [];
        foreach ($deliveryResults as $row) {
            $diffLabel = null;
            $status = null;
            if ($row->actual_date && $row->promised_date) {
                $actual = \Carbon\Carbon::parse($row->actual_date);
                $promised = \Carbon\Carbon::parse($row->promised_date);
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
                'material_description' => $row->material_description,
                'ordered_qty'          => $row->ordered_qty,
                'delivered_qty'        => $row->delivered_qty,
                'promised_date'        => $row->promised_date,
                'actual_date'          => $row->actual_date,
                'days_diff'            => $diffLabel,
            ];
        }

        // GRN Report
        $grnQuery = GrnDelivery::query()
            ->select(
                'purchase_doc_no as po_number',
                'purchase_doc_no as grn_num',
                'posting_date as grn_date',
                'item_number as line_item',
                'Quantity as quantity_supplied',
                'Quantity as qty_received_by_amg', // Assuming same for now
                'plant as storage_location'
            );
        if ($request->filled('grn_line_no')) {
            $grnQuery->where('item_mat_doc', $request->grn_line_no);
        }
        if ($request->filled('grn_po_number')) {
            $grnQuery->where('po_number', $request->grn_po_number);
        }
        $grnResults = $grnQuery->orderBy('posting_date', 'desc')->get();
        $grnReports = $grnResults->map(function($item) {
            // Use the po_number already present in the result
            $po_number = $item->po_number;
            // Join delivery_items -> deliveries -> purchase_orders to get remarks for correct PO number and line item
            $remarks = \DB::table('delivery_items')
                ->join('deliveries', 'delivery_items.delivery_id', '=', 'deliveries.id')
                ->join('purchase_orders', 'deliveries.order_id', '=', 'purchase_orders.id')
                ->where('purchase_orders.order_number', $po_number)
                ->where('delivery_items.line_item_num', $item->line_item)
                ->value('delivery_items.remarks');
            $supplied = (int) $item->quantity_supplied;
            $received = (int) $item->qty_received_by_amg;
            $diff = $supplied - $received;
            $item->po_number = $po_number;
            $item->remarks = $remarks;
            $item->shortfall_excess = ($diff === 0) ? 'No' : 'Yes';
            return $item;
        });

        // Invoice Status Report
        $invoiceStatuses = \DB::table('invoices')->select('status')->distinct()->pluck('status')->filter()->values();
        $invoiceQuery = InvoiceData::query()
            ->select(
                'account_doc_no as invoice_number',
                'purchase_doc_no as po_number',
                'document_dt as invoice_date',
                'amount as invoice_amount',
                'reference_docs as miro_document',
                'baseline_due_dt as expected_payment_date'
            );
        if ($request->filled('invoice_po_number')) {
            $invoiceQuery->where('purchase_doc_no', $request->invoice_po_number);
        }
        $invoiceResults = $invoiceQuery->orderBy('document_dt', 'desc')->get();
        $invoiceReports = $invoiceResults->map(function($inv) {
            // Fetch invoice status and rejection reason from invoices table
            $legacy = \DB::table('invoices')->where('invoice_number', $inv->invoice_number)->first();
            return (object) [
                'invoice_number' => $inv->invoice_number,
                'po_number' => $inv->po_number,
                'invoice_date' => $inv->invoice_date,
                'invoice_amount' => $inv->invoice_amount,
                'miro_document' => $inv->miro_document,
                'invoice_status' => $legacy->status ?? null,
                'rejection_reason' => $legacy->rejection_reason ?? null,
                'expected_payment_date' => $inv->expected_payment_date,
            ];
        });

        // Payment Status Report
        $paymentQuery = InvoiceData::query()
            ->select(
                'purchase_doc_no as invoice_number',
                'account_doc_no as payment_document_number',
                'post_doc_date as payment_date',
                'amount',
                'reference_docs',
                'dc_indicator as status',
                'add_text3 as deductions'
            );
        if ($request->filled('payment_po_number')) {
            $paymentQuery->where('purchase_doc_no', $request->payment_po_number);
        }
        if ($request->filled('payment_status')) {
            $paymentQuery->where('dc_indicator', $request->payment_status);
        }
        $paymentResults = $paymentQuery->orderBy('post_doc_date', 'desc')->get();
        $paymentReports = $paymentResults->map(function($pay) {
            $deductions = is_numeric($pay->deductions) ? (float)$pay->deductions : 0;
            $amount = is_numeric($pay->amount) ? (float)$pay->amount : 0;
            return (object) [
                'invoice_num' => $pay->invoice_number,
                'payment_document_number' => $pay->payment_document_number,
                'payment_date' => $pay->payment_date,
                'amount' => $amount,
                'reference_number' => $pay->reference_docs,
                'status' => $pay->status,
                'deductions' => $deductions,
                'balance_outstanding' => $amount - $deductions,
            ];
        });

        // --- Vendor Performance Dashboard logic updated to use new tables ---
        $totalPos = PoMaster::count();
        // No status column, so acknowledgedOnTime is set to 0
        $acknowledgedOnTime = 0;
        $acknowledgedPercent = $totalPos > 0 ? round(($acknowledgedOnTime / $totalPos) * 100, 2) : 0;
        // On-Time Delivery: use PoDelivery stat_del_dt (actual) vs itm_delivery_dt (promised)
        $onTimeDeliveries = PoDelivery::whereNotNull('stat_del_dt')
            ->whereColumn('stat_del_dt', '<=', 'itm_delivery_dt')
        ->count();
        $totalDeliveries = PoDelivery::whereNotNull('stat_del_dt')->count();
        $onTimeDeliveryPercent = $totalDeliveries > 0 ? round(($onTimeDeliveries / $totalDeliveries) * 100, 2) : 0;
        // Quarter-over-quarter delays: group by quarter using purchase_order_dt
        $quarterDelays = PoMaster::selectRaw("
                CONCAT(YEAR(purchase_order_dt), ' Q', QUARTER(purchase_order_dt)) as quarter,
                COUNT(*) as total_orders
        ")
        ->groupBy('quarter')
        ->orderBy('quarter')
        ->get();
    $delayChartLabels = $quarterDelays->pluck('quarter');
    $delayChartData = $quarterDelays->map(function ($row) {
            // No delayed_orders info, set as 0
            return 0;
    });

        // --- Backlog Report using new tables ---
        $backlogQuery = PoDelivery::query()
            ->join('v002_pomaster', 'v002_pomaster.purchase_doc_no', '=', 'v003_podelivery.prchase_doc_number')
            ->select(
                'v002_pomaster.purchase_doc_no as po_number',
                'v002_pomaster.purchase_order_dt as order_date',
                'v003_podelivery.item_no as line_item',
                'v003_podelivery.schedule_qty as ordered_qty',
                'v003_podelivery.goods_qty as delivered_qty',
                'v003_podelivery.itm_delivery_dt as delivery_date'
            );
        if ($request->filled('backlog_po_number')) {
            $backlogQuery->where('v002_pomaster.purchase_doc_no', $request->backlog_po_number);
        }
        if ($request->filled('backlog_line_item')) {
            $backlogQuery->where('v003_podelivery.item_no', $request->backlog_line_item);
        }
        $backlogResults = $backlogQuery->orderBy('v003_podelivery.itm_delivery_dt', 'asc')->get();
        $backlogReports = [];
        foreach ($backlogResults as $item) {
            // Fetch item_description from delivery_items
            $item_description = \DB::table('delivery_items')
                ->join('deliveries', 'delivery_items.delivery_id', '=', 'deliveries.id')
                ->join('purchase_orders', 'deliveries.order_id', '=', 'purchase_orders.id')
                ->where('purchase_orders.order_number', $item->po_number)
                ->where('delivery_items.line_item_num', $item->line_item)
                ->value('delivery_items.item_description');
            $backlogReports[] = (object) [
                'po_number' => $item->po_number,
                'line_item' => $item->line_item,
                'item_description' => $item_description,
                'ordered_qty' => $item->ordered_qty,
                'delivered_qty' => $item->delivered_qty,
                'pending_qty' => ($item->ordered_qty ?? 0) - ($item->delivered_qty ?? 0),
                'delivery_date' => $item->delivery_date,
            ];
        }

        // --- Returns Report using new table ---
$returnStatuses = \App\Models\Returns::select('follow_up_status')
    ->distinct()
    ->whereNotNull('follow_up_status')
    ->pluck('follow_up_status')
    ->filter()
    ->values();
$query = \App\Models\Returns::query();
if ($request->filled('returns_po_number')) {
            $query->where('po_number', $request->returns_po_number);
        }
if ($request->filled('returns_line_item')) {
            $query->where('material_code', $request->returns_line_item);
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
