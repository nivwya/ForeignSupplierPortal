<?php
namespace App\Http\Controllers;
use App\Models\Delivery;
use App\Models\DeliveryItem;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\VendorContact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\PoMaster;
use App\Models\PoDelivery;
use App\Models\GrnDelivery;
class DeliveryTabController extends Controller
{
    public function deliveryOrderItems($orderId)
    {
        $user = auth()->user();
        $order = PoMaster::where('purchase_doc_no', $orderId)->firstOrFail();
        $items = PoDelivery::where('prchase_doc_number', $orderId)->get();
        // Optionally, fetch GRN info for each item if needed
        return view('tabs.delivery_order_items', [
            'order' => $order,
            'items' => $items,
        ]);
    }
    public function newDeliveryRow($orderId, $deliveryItemId)
    {
        $deliveryItem = DeliveryItem::with(['purchaseOrderItem', 'delivery'])->findOrFail($deliveryItemId);
        // Render a blade partial for a single row
        return view('partials.delivery_row', ['deliveryItem' => $deliveryItem])->render();
    }
    public function deliveriesTab(Request $request)
    {
        $user = Auth::user();
        $deliveries = collect();
        $acknowledgedPOs = collect();
        $query = PoDelivery::query()
            ->join('v002_pomaster', 'v002_pomaster.purchase_doc_no', '=', 'v003_podelivery.prchase_doc_number')
            ->leftJoin('v004_grn_delivery', function($join) {
                $join->on('v004_grn_delivery.purchase_doc_no', '=', 'v003_podelivery.prchase_doc_number')
                     ->on('v004_grn_delivery.item_number', '=', 'v003_podelivery.item_no');
            })
            ->leftJoin('deliveries', 'deliveries.order_id', '=', 'v003_podelivery.prchase_doc_number')
            ->select([
                'v003_podelivery.prchase_doc_number',
                'v003_podelivery.order_dt',
                'v003_podelivery.itm_delivery_dt',
                'v002_pomaster.company_name',
                'v002_pomaster.company_name as department',
                'v002_pomaster.net_price',
                'v002_pomaster.purchase_order_qty',
                'v002_pomaster.currency_key',
                'v003_podelivery.schedule_qty',
                'v003_podelivery.goods_qty',
                'deliveries.status as status',
                'v004_grn_delivery.purchase_doc_no',
                'v004_grn_delivery.posting_date',
            ]);
        if (!$user->hasRole('admin')) {
            $email = $user->email;
            $vendorContact = VendorContact::where('email', $email)->first();
            if ($vendorContact) {
                $query->where('v002_pomaster.vendor_account_no', $vendorContact->vendor_id);
            } else {
                if ($request->ajax()) {
                    return view('tabs.deliveries_table', compact('deliveries', 'acknowledgedPOs'))->render();
                }
                return view('tabs.deliveries', compact('deliveries', 'acknowledgedPOs', 'user'));
            }
        }
        if ($request->filled('order_number')) {
            $query->where('v003_podelivery.prchase_doc_number', 'like', '%' . $request->order_number . '%');
        }
        if ($request->filled('company')) {
            $query->where('v002_pomaster.company_name', 'like', '%' . $request->company . '%');
        }
        if ($request->filled('department')) {
            $query->where('v002_pomaster.company_name', 'like', '%' . $request->department . '%');
        }
        // Status filter (if needed)
        if ($request->filled('status')) {
            $query->where('deliveries.status', $request->status);
        }
        $deliveries = $query->get();
        if (!$user->hasRole('admin')) {
            $vendorContact = VendorContact::where('email', $user->email)->first();
            if ($vendorContact) {
                $acknowledgedPOs = PoMaster::where('vendor_account_no', $vendorContact->vendor_id)
                    ->whereHas('deliveriesRelation', function($q) {
                        $q->where('status', 'acknowledged');
                    })
                    ->get();
            }
        }
        if ($request->ajax()) {
            return view('tabs.deliveries_table', compact('deliveries', 'acknowledgedPOs'))->render();
        }
        return view('tabs.deliveries', compact('deliveries', 'acknowledgedPOs', 'user'));
    }
    public function makeDelivery(Request $request, $id)
    {
        $user = Auth::user();
        $purchaseOrder = PurchaseOrder::with('items')->findOrFail($id);
        if (!$user->hasRole('admin')) {
            $email = $user->email;
            $vendorContact = VendorContact::where('email', $email)
                ->where('vendor_id', $purchaseOrder->vendor_id)
                ->first();
            if (!$vendorContact) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
        }
        if ($purchaseOrder->status !== 'acknowledged') {
            return response()->json(['success' => false, 'message' => 'PO must be acknowledged first.'], 400);
        }
        if ($purchaseOrder->deliveries()->exists()) {
            return response()->json(['success' => false, 'message' => 'Delivery already exists for this PO.'], 400);
        }
        $maxDeliveryNumber = Delivery::max('delivery_number') ?? 100000;
        $nextDeliveryNumber = $maxDeliveryNumber + 1;
        $delivery = Delivery::create([
            'order_id' => $purchaseOrder->id,
            'delivery_date' => now()->toDateString(),
            'delivery_number' => $nextDeliveryNumber,
            'company' => $purchaseOrder->company,
            'department' => $purchaseOrder->department,
            'order_value' => $purchaseOrder->order_value,
            'currency' => $purchaseOrder->currency,
            'status' => 'partial',
            'confirmed_by' => $user->id,
            'confirmed_at' => now(),
        ]);
        foreach ($purchaseOrder->items as $poItem) {
            $delivery->items()->create([
                'purchase_order_item_id' => $poItem->id,
                'line_item_num' => $poItem->line_item_no,
                'item_description' => $poItem->item_description,
                'quantity' => $poItem->quantity,
                'uom' => $poItem->uom,
                'expected_delv_date' => $poItem->delivery_date ?? $purchaseOrder->delivery_date,
                'quantity_supplied' => 0,
                'supply_date' => null,
                'qty_received_by_amg' => 0,
                'amg_received_date' => null,
                'status' => 'PARTIAL',
                'unit_price' => $poItem->price,
                'total_value' => 0,
            ]);
        }
        $purchaseOrder->update(['status' => 'partial delivery']);
        return response()->json(['success' => true, 'message' => 'Delivery created successfully!']);
    }
    public function reportSuppliedQuantity(Request $request, $id)
    {
        $user = Auth::user();
        $deliveryItem = DeliveryItem::with('delivery.purchaseOrder')->findOrFail($id);

        if (!$user->hasRole('admin')) {
            $vendorContact = VendorContact::where('email', $user->email)
                ->where('vendor_id', $deliveryItem->delivery->purchaseOrder->vendor_id)
                ->first();
            if (!$vendorContact) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
        }
        $poItem = $deliveryItem->purchaseOrderItem;
        $orderedQty = $poItem->quantity;
        $totalSupplied = DeliveryItem::where('purchase_order_item_id', $poItem->id)
        ->where('id', '!=', $deliveryItem->id)
        ->sum('quantity_supplied');
        $maxQty = $orderedQty - $totalSupplied;
        $orderDate = $deliveryItem->delivery->purchaseOrder->order_date;
        $messages = [
            'quantity_supplied.max' => 'The supplied quantity cannot exceed the remaining ordered quantity.',
            'supply_date.after' => 'The supply date must be after the order date.',
        ];
        $validated = $request->validate([
        'quantity_supplied' => ['required', 'numeric', 'min:0', 'max:' . $maxQty],
        'supply_date' => [
            'required',
            'date',
            function ($attribute, $value, $fail) use ($orderDate) {
                if (strtotime($value) <= strtotime($orderDate)) {
                    $fail('The supply date must be after the order date ('.$orderDate.').');
                }
            }
         ],
        ], $messages);
        $deliveryItem->update([
            'quantity_supplied' => $validated['quantity_supplied'],
            'supply_date' => $validated['supply_date'] ?? now()->toDateString(),
            'total_value' => $validated['quantity_supplied'] * $deliveryItem->unit_price,
            'status' => ($validated['quantity_supplied'] > 0) ? 'PARTIAL' : 'PENDING'
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Quantity supplied updated successfully'
        ]);
    }
    public function verifyReceivedQuantity(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user->hasRole('admin')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        $deliveryItem = DeliveryItem::findOrFail($id);
        $validated = $request->validate([
            'qty_received_by_amg' => 'required|numeric|min:0',
            'amg_received_date' => 'nullable|date'
        ]);
        $deliveryItem->update([
            'qty_received_by_amg' => $validated['qty_received_by_amg'],
            'amg_received_date' => $validated['amg_received_date'] ?? now()->toDateString()
        ]);
        if ($deliveryItem->qty_received_by_amg == $deliveryItem->quantity_supplied) {
            $deliveryItem->update(['status' => 'DELIVERED']);
        } else {
            $deliveryItem->update(['status' => 'VARIANCE']);
        }
        return response()->json([
            'success' => true,
            'message' => 'Quantity received verified successfully'
        ]);
    }
  public function batchSave(Request $request, $orderId)
{
    $user = auth()->user();
    $deliveryItemIds = $request->input('delivery_item_id', []);
    $quantities = $request->input('quantity_supplied', []);
    $dates = $request->input('supply_date', []);
    $pdfs = $request->file('delivery_note', []);
    foreach ($deliveryItemIds as $i => $id) {
        $item = \App\Models\DeliveryItem::find($id);
        if (!$item || $item->quantity_supplied > 0) continue;
        $poItem = $item->purchaseOrderItem;
        $ordered = $poItem->quantity;
        $alreadySupplied = \App\Models\DeliveryItem::where('purchase_order_item_id', $poItem->id)
            ->where('id', '!=', $item->id)
            ->sum('quantity_supplied');
        $maxQty = $ordered - $alreadySupplied;
        $qty = (float)($quantities[$i] ?? 0);
        if ($qty < 0 || $qty > $maxQty) continue;
        $item->quantity_supplied = $qty;
        $item->supply_date = $dates[$i] ?? now()->toDateString();
        if (isset($pdfs[$i]) && $pdfs[$i]->isValid()) {
            $filenameOnly = uniqid() . '.' . $pdfs[$i]->getClientOriginalExtension();
            $storagePath = 'delivery_pdfs/' . $filenameOnly;
            Storage::disk('public')->makeDirectory('delivery_pdfs');
            $stream = fopen($pdfs[$i]->getRealPath(), 'r+');
            $stored = Storage::disk('public')->put($storagePath, $stream);
            if ($stored) {
                \Log::info("File stored to delivery_pdfs", ['filename' => $storagePath]);
                $item->delivery_note = $storagePath;
            } else {
                \Log::error("Failed to store delivery note", ['filename' => $storagePath]);
            }
        } else {
            \Log::warning("PDF at index $i is missing or invalid");
        }

        $item->save();
    }
    $order = \App\Models\PurchaseOrder::with(['items', 'deliveries.items'])->findOrFail($orderId);
    $html = view('tabs.delivery_order_items', ['order' => $order])->render();
    return response()->json(['html' => $html]);
}
    public function addPartialDelivery(Request $request, $poItemId)
    {
        $user = Auth::user();
        $poItem = PurchaseOrderItem::with('purchaseOrder')->findOrFail($poItemId);
        if (!$user->hasRole('admin')) {
            $vendorContact = VendorContact::where('email', $user->email)
                ->where('vendor_id', $poItem->purchaseOrder->vendor_id)
                ->first();
            if (!$vendorContact) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
        }
        $totalSupplied = DeliveryItem::where('purchase_order_item_id', $poItemId)->sum('quantity_supplied');
        $remainingQty = $poItem->quantity - $totalSupplied;
        if ($remainingQty <= 0) {
            return response()->json(['success' => false, 'message' => 'No remaining quantity for this item']);
        }
        $delivery = Delivery::where('order_id', $poItem->order_id)->first();
        if (!$delivery) {
        $delivery = Delivery::create([
            'order_id' => $poItem->order_id,
            'delivery_date' => now()->toDateString(),
            'delivery_number' => (Delivery::max('delivery_number') ?? 100000) + 1,
            'company' => $poItem->purchaseOrder->company,
            'department' => $poItem->purchaseOrder->department,
            'order_value' => $poItem->purchaseOrder->order_value,
            'currency' => $poItem->purchaseOrder->currency,
            'status' => 'partial',
            'confirmed_by' => $user->id,
            'confirmed_at' => now(),
          ]);
        }
        $deliveryItem = DeliveryItem::create([
            'delivery_id' => $delivery->id,
            'purchase_order_item_id' => $poItem->id,
            'line_item_num' => $poItem->line_item_no,
            'item_description' => $poItem->item_description,
            'quantity' => $remainingQty,
            'uom' => $poItem->uom,
            'expected_delv_date' => $poItem->delivery_date ?? $poItem->purchaseOrder->delivery_date,
            'quantity_supplied' => 0,
            'status' => 'PARTIAL',
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Partial delivery added.',
            'delivery_item' => $deliveryItem
        ]);
    }
}
