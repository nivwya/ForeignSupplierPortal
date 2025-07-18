<?php

namespace App\Http\Controllers;

use App\Services\InvoiceService;
use App\Models\DeliveryItem;
use Illuminate\Http\Request;

class SapWebhookController extends Controller
{
    protected $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    public function handle(Request $request)
    {
        // Validate SAP signature/auth as needed
        $data = $request->all();
        
        // Find the relevant DeliveryItem by PO/line/item number
        $item = DeliveryItem::where('line_item_num', $data['line_item_no'])
            ->whereHas('delivery.purchaseOrder', function($q) use ($data) {
                $q->where('order_number', $data['po_number']);
            })->first();

        if ($item) {
            $item->qty_received_by_amg = $data['qty_received'];
            $item->amg_received_date = $data['received_date'];
            $item->save();

            // Check if this delivery is now complete and create invoice if needed
            $this->invoiceService->checkAndCreateInvoice($item->delivery_id);
        }

        return response()->json(['success' => true]);
    }
}
