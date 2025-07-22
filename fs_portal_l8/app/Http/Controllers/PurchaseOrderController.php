<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Vendor;
use App\Models\VendorContact;
use App\Models\Delivery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class PurchaseOrderController extends Controller
{
    /**
     * List all purchase orders.
     */
    public function index(Request $request)
    {
        $user = auth('web')->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        if ($user->hasRole('admin')) {
            $purchaseOrders = PurchaseOrder::with(['vendor', 'items'])->get();
        } else {
            $email = $user->email;
            $vendorContact = VendorContact::where('email', $email)->first();

            if (!$vendorContact) {
                return response()->json(['success' => false, 'message' => 'No vendor associated with this email'], 404);
            }
            if (!$vendorContact) {
            \Log::warning("No VendorContact found for user email: $email");
            return response()->json(['success' => false, 'message' => 'No vendor associated with this email'], 404);
        }

            $purchaseOrders = PurchaseOrder::with(['items'])
                ->where('vendor_id', $vendorContact->vendor_id)
                ->get();
        }

        return response()->json([
            'success' => true,
            'data' => $purchaseOrders
        ]);
    }

    /**
     * Store a new purchase order (admin only).
     */
   public function store(Request $request)
    {
    $user = auth('web')->user();
    if (!$user) {
        return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
    }
    if (!$user->hasRole('admin')) {
        return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
    }

        $validated = $request->validate([
            'order_number' => 'required|unique:purchase_orders',
            'vendor_id' => 'required|exists:vendors,id',
            'order_date' => 'required|date',
            'delivery_date' => 'required|date',
            'company' => 'required|string|max:100',
            'department' => 'required|string|max:100',
            'currency' => 'required|string|max:10',
            'payment_term' => 'required|string|max:20',
            'status' => 'required|string|max:50',
            'po_pdf' => 'required|file',
            'items' => 'required|array|min:1',
            'items.*.product_code' => 'required|string|max:50',
            'items.*.line_item_no' => 'nullable|integer',
            'items.*.item_description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.uom' => 'required|string|max:10',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.value' => 'nullable|numeric',
            'items.*.plant' => 'nullable|string|max:20',
            'items.*.slocc' => 'nullable|string|max:20',
            'items.*.status' => 'nullable|string|max:50',
            'items.*.delivery_date' => 'nullable|date',
            'items.*.material_group' => 'nullable|string|max:20',
            'items.*.delivered_quantity' => 'nullable|numeric',
            'items.*.invoiced_quantity' => 'nullable|numeric',
        ]);

        // Store PO PDF
        $poPdfPath = $request->file('po_pdf')->store('purchase_orders');

        // Calculate order value from items
        $orderValue = 0;
        foreach ($request->items as $item) {
            $orderValue += $item['quantity'] * $item['price'];
        }
        $validated['order_value'] = $orderValue;

        // Create the purchase order (add 'po_pdf' to fillable in the model!)
        $purchaseOrder = PurchaseOrder::create([
            'order_number'   => $validated['order_number'],
            'vendor_id'      => $validated['vendor_id'],
            'order_date'     => $validated['order_date'],
            'delivery_date'  => $validated['delivery_date'],
            'company'        => $validated['company'],
            'department'     => $validated['department'],
            'order_value'    => $validated['order_value'],
            'currency'       => $validated['currency'],
            'payment_term'   => $validated['payment_term'],
            'status'         => $validated['status'],
            'po_pdf'         => $poPdfPath,
        ]);

        // Create items
        foreach ($validated['items'] as $item) {
            PurchaseOrderItem::create([
                'order_id'           => $purchaseOrder->id,
                'product_code'       => $item['product_code'],
                'line_item_no'       => $item['line_item_no'] ?? null,
                'item_description'   => $item['item_description'],
                'quantity'           => $item['quantity'],
                'uom'                => $item['uom'],
                'price'              => $item['price'],
                'value'              => $item['quantity'] * $item['price'],
                'plant'              => $item['plant'] ?? null,
                'slocc'              => $item['slocc'] ?? null,
                'status'             => $item['status'] ?? 'OPEN',
                'delivery_date'      => $item['delivery_date'] ?? null,
                'material_group'     => $item['material_group'] ?? null,
                'delivered_quantity' => $item['delivered_quantity'] ?? 0,
                'invoiced_quantity'  => $item['invoiced_quantity'] ?? 0,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Purchase order created successfully',
            'data'    => $purchaseOrder->load('items')
        ], 201);
    }

    /**
     * Display the specified purchase order
     */
    public function show(Request $request, $id)
    {
        $user = auth('web')->user();
        $purchaseOrder = PurchaseOrder::with(['items', 'vendor', 'deliveries'])->findOrFail($id);
        
        if (!$user->hasRole('admin')) {
            // Check if user is associated with the vendor
            $email = $user->email;
            $vendorContact = VendorContact::where('email', $email)
                ->where('vendor_id', $purchaseOrder->vendor_id)
                ->first();
                
            if (!$vendorContact) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
        }
        
        return response()->json([
            'success' => true,
            'data' => $purchaseOrder
        ]);
    }

    public function orderItems($orderId)
        {
            $user = auth()->user();
            $order = \App\Models\PurchaseOrder::with('items')->findOrFail($orderId);

            // Optionally, check vendor access like you do elsewhere
            if (!$user->hasRole('admin')) {
                $vendorContact = \App\Models\VendorContact::where('email', $user->email)
                    ->where('vendor_id', $order->vendor_id)
                    ->first();
                if (!$vendorContact) {
                    return response('<div style="color:red;">Unauthorized</div>', 403);
                }
            }

            // Return a Blade partial with the items table
            return view('tabs.order_items', ['items' => $order->items, 'order' => $order]);
        }


    /**
     * Download PO PDF ITS CALLED DOWNLOADPDF BUT IT SUPPORTS ALL TYPE OF FILES
     */
    public function downloadPdf(Request $request, $id) 
{
    $user = auth()->user();
    $purchaseOrder = PurchaseOrder::findOrFail($id);
    
    if (!$user->hasRole('admin')) {
        // Check if user is associated with the vendor
        $email = $user->email;
        $vendorContact = VendorContact::where('email', $email)
            ->where('vendor_id', $purchaseOrder->vendor_id)
            ->first();
            
        if (!$vendorContact) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
    }
    
    if (!$purchaseOrder->po_pdf || !Storage::exists($purchaseOrder->po_pdf)) {
        return response()->json(['success' => false, 'message' => 'File not found'], 404);
    }
    
    $filename = 'purchase_order_' . ($purchaseOrder->order_number ?? $purchaseOrder->id) . '.pdf';
    return Storage::download($purchaseOrder->po_pdf, $filename);
}


    public function confirmDelivery(Request $request, $id)
        {
            $user = auth('web')->user();
            if (!$user->hasRole('admin')) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            $purchaseOrder = PurchaseOrder::with(['items', 'deliveries'])->findOrFail($id);

            // Only allow confirmation if PO is still pending/not delivered
            if (!in_array($purchaseOrder->status, ['issued', 'pending', 'not delivered', 'acknowledged'])) {
                return response()->json(['success' => false, 'message' => 'PO cannot be confirmed in current state'], 400);
            }

            // Validate item-level delivery data
            $validated = $request->validate([
                'items' => 'required|array|min:1',
                'items.*.purchase_order_item_id' => [
                    'required',
                    'exists:purchase_order_items,id'
                ],
                'items.*.delivered_quantity' => 'required|numeric|min:0.01',
                'items.*.grn_pdf' => 'nullable|file',
                'notes' => 'nullable|string',
            ]);

            // Generate unique delivery_number
            $maxDeliveryNumber = \App\Models\Delivery::max('delivery_number') ?? 100000;
            $nextDeliveryNumber = $maxDeliveryNumber + 1;

            // Create delivery header
            $delivery = \App\Models\Delivery::create([
                'order_id' => $purchaseOrder->id,
                'delivery_date' => now()->toDateString(),
                'delivery_number' => $nextDeliveryNumber,
                'company' => $purchaseOrder->company,
                'department' => $purchaseOrder->department,
                'order_value' => $purchaseOrder->order_value,
                'currency' => $purchaseOrder->currency,
                'status' => 'partial', // Default status, will update later
                'notes' => $validated['notes'] ?? null,
                'confirmed_by' => $user->id,
                'confirmed_at' => now(),
            ]);

            $allItemsFullyDelivered = true;
            $anyItemsDelivered = false;

            // Build a map of PO item IDs for quick lookup
            $poItemsMap = $purchaseOrder->items->keyBy('id');

            foreach ($validated['items'] as $index => $itemData) {
                $poItem = $poItemsMap->get($itemData['purchase_order_item_id']);

                if (!$poItem) {
                    // This should not happen due to validation, but just in case
                    return response()->json([
                        'success' => false,
                        'message' => "Item at index {$index} does not belong to this purchase order."
                    ], 400);
                }

                $deliveredQty = (float)$itemData['delivered_quantity'];
                if ($deliveredQty <= 0) continue; // Skip items with no delivery

                $anyItemsDelivered = true;
                $remainingQty = $poItem->quantity - $poItem->quantity_supplied;

                // Validate delivery quantity
                if ($deliveredQty > $remainingQty) {
                    return response()->json([
                        'success' => false,
                        'message' => "Delivery quantity exceeds remaining quantity for item {$poItem->id}"
                    ], 400);
                }

                // Handle file upload if present
                $grnPdfPath = null;
                if (isset($itemData['grn_pdf']) && $itemData['grn_pdf']) {
                    $grnPdfPath = $itemData['grn_pdf']->store('grns');
                }

                // Create delivery item record
                $deliveryItem = \App\Models\DeliveryItem::create([
                    'delivery_id' => $delivery->id,
                    'purchase_order_item_id' => $poItem->id,
                    'line_item_num' => $poItem->line_item_no,
                    'item_description' => $poItem->item_description,
                    'quantity' => $deliveredQty,
                    'uom' => $poItem->uom,
                    'expected_delv_date' => $purchaseOrder->delivery_date , // Use validated value
                    'quantity_supplied' => $deliveredQty,
                    'supply_date' => now(),
                    'status' => ($deliveredQty == $remainingQty) ? 'DELIVERED' : 'PARTIAL',
                    'grn_pdf' => $grnPdfPath,
                    'unit_price' => $poItem->unit_price,
                    'total_value' => $poItem->unit_price * $deliveredQty,
                ]);

                // Update PO item status and supplied quantity
                $poItem->quantity_supplied += $deliveredQty;
                $poItem->status = ($poItem->quantity_supplied == $poItem->quantity)
                    ? 'DELIVERED'
                    : 'PARTIAL';
                $poItem->save();

                // Track if all items are fully delivered
                if ($poItem->quantity_supplied < $poItem->quantity) {
                    $allItemsFullyDelivered = false;
                }
            }

            if (!$anyItemsDelivered) {
                // No items delivered, rollback and respond
                $delivery->delete();
                return response()->json([
                    'success' => false,
                    'message' => 'No items were delivered. Please provide delivered quantities.'
                ], 400);
            }

            // Update delivery and PO status
            $deliveryStatus = $allItemsFullyDelivered ? 'delivered' : 'partial';
            $poStatus = $allItemsFullyDelivered ? 'delivered' : 'partial delivery';

            $delivery->update(['status' => $deliveryStatus]);
            $purchaseOrder->update(['status' => $poStatus]);

            return response()->json([
                'success' => true,
                'message' => 'Delivery processed successfully',
                'data' => [
                    'delivery' => $delivery->fresh('items'),
                ]
            ]);
        }


    public function update(Request $request, $id)
        {
            return response()->json(['message' => 'Not implemented'], 501);
        }


   public function acknowledge(Request $request, $id)
    {
        $user = auth('web')->user();
        $purchaseOrder = PurchaseOrder::findOrFail($id);

        // Only allow vendor to acknowledge their own PO
        if (!$user->hasRole('admin')) {
            $email = $user->email;
            $vendorContact = VendorContact::where('email', $email)
                ->where('vendor_id', $purchaseOrder->vendor_id)
                ->first();
            if (!$vendorContact) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
        }

        if ($purchaseOrder->status !== 'issued') {
            return response()->json(['success' => false, 'message' => 'PO cannot be acknowledged in current state'], 400);
        }

       $purchaseOrder->update([
            'status' => 'acknowledged',
            'acknowledgement_date' => now(),
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Purchase order acknowledged',
            'data' => $purchaseOrder
        ]);
    }


    public function makeDelivery(Request $request, $id)
{
    $user = auth('web')->user();
    $purchaseOrder = PurchaseOrder::with('items')->findOrFail($id);

    // Only allow if PO is acknowledged
    if ($purchaseOrder->status !== 'acknowledged') {
        return response()->json(['success' => false, 'message' => 'PO must be acknowledged first.'], 400);
    }

    // Prevent duplicate deliveries for same PO
    if ($purchaseOrder->deliveries()->exists()) {
        return response()->json(['success' => false, 'message' => 'Delivery already exists for this PO.'], 400);
    }

    // Generate unique delivery number
    $maxDeliveryNumber = Delivery::max('delivery_number') ?? 100000;
    $nextDeliveryNumber = $maxDeliveryNumber + 1;

    // Create Delivery
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

    // Create DeliveryItem for each PO item
    foreach ($purchaseOrder->items as $poItem) {
        $delivery->items()->create([
            'purchase_order_item_id' => $poItem->id,
            'line_item_num' => $poItem->line_item_no,
            'item_description' => $poItem->item_description,
            'quantity' => $poItem->quantity,
            'uom' => $poItem->uom,
            'expected_delv_date' => $poItem->delivery_date ?? $purchaseOrder->delivery_date,
            'quantity_supplied' => 0, // Vendor will fill in
            'supply_date' => null,
            'status' => 'PARTIAL',
        ]);
    }

    // Update PO status
    $purchaseOrder->update(['status' => 'partial delivery']);

    return response()->json(['success' => true, 'message' => 'Delivery created!']);
}    


    /**
     * Cancel a purchase order (admin only)
     */
    public function cancel(Request $request, $id)
    {
        $user = auth('web')->user();
        if (!$user->hasRole('admin')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        $purchaseOrder = PurchaseOrder::findOrFail($id);
        
        if (!in_array($purchaseOrder->status, ['issued', 'draft'])) {
            return response()->json(['success' => false, 'message' => 'Cannot cancel PO in current state'], 400);
        }
        
        $purchaseOrder->update([
            'status' => 'cancelled',
            'cancelled_by' => $user->id,
            'cancelled_at' => now(),
        ]);

        $purchaseOrder->items()->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Purchase order cancelled',
            'data' => $purchaseOrder
        ]);
    }
}
