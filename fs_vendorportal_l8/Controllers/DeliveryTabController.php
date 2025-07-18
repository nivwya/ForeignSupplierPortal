<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\DeliveryItem;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\VendorContact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeliveryTabController extends Controller
{
    /**
     * Display deliveries tab content
     */

    public function deliveryOrderItems($orderId)
   {
        $user = auth()->user();
        $order = \App\Models\PurchaseOrder::with(['items', 'deliveries.items'])->findOrFail($orderId);

        // Optionally, restrict access to vendor/admin as you do elsewhere

        // Render only the item details table as a Blade partial
        return view('tabs.delivery_order_items', [
            'order' => $order,
            'items' => $order->items,
            'deliveries' => $order->deliveries
        ]);
    }
    public function deliveriesTab(Request $request)
{
    $user = Auth::user();
    $query = Delivery::query();

    if ($user->hasRole('admin')) {
        $query->with(['purchaseOrder.vendor', 'items.purchaseOrderItem']);
    } else {
        $email = $user->email;
        $vendorContact = VendorContact::where('email', $email)->first();
        if (!$vendorContact) {
            $deliveries = collect();
            $acknowledgedPOs = collect();
            if ($request->ajax()) {
                return view('tabs.deliveries_table', compact('deliveries', 'acknowledgedPOs'))->render();
            }
            return view('tabs.deliveries', compact('deliveries', 'acknowledgedPOs', 'user'));
        }
        $query->whereHas('purchaseOrder', function($q) use ($vendorContact) {
            $q->where('vendor_id', $vendorContact->vendor_id);
        })->with(['purchaseOrder.vendor', 'items.purchaseOrderItem']);
    }

    // Apply filters
    if ($request->filled('order_number')) {
        $query->whereHas('purchaseOrder', function($q) use ($request) {
            $q->where('order_number', 'like', '%' . $request->order_number . '%');
        });
    }
    if ($request->filled('company')) {
        $query->whereHas('purchaseOrder', function($q) use ($request) {
            $q->where('company', 'like', '%' . $request->company . '%');
        });
    }
    if ($request->filled('department')) {
        $query->whereHas('purchaseOrder', function($q) use ($request) {
            $q->where('department', 'like', '%' . $request->department . '%');
        });
    }
    if ($request->filled('status')) {
        $query->whereHas('purchaseOrder', function($q) use ($request) {
            $q->where('status', $request->status);
        });
    }

    $deliveries = $query->get();

    // For each delivery item, calculate total supplied for its PO line
    foreach ($deliveries as $delivery) {
        foreach ($delivery->items as $item) {
            $item->total_supplied = DeliveryItem::where('purchase_order_item_id', $item->purchase_order_item_id)
                ->sum('quantity_supplied');
            $item->order_quantity = $item->purchaseOrderItem->quantity;
        }
    }

    // Acknowledged POs (no delivery)
    if ($user->hasRole('admin')) {
        $acknowledgedPOs = PurchaseOrder::where('status', 'acknowledged')
            ->whereDoesntHave('deliveries')
            ->with(['vendor', 'items'])
            ->get();
    } else {
        $acknowledgedPOs = PurchaseOrder::where('vendor_id', $vendorContact->vendor_id)
            ->where('status', 'acknowledged')
            ->whereDoesntHave('deliveries')
            ->with(['vendor', 'items'])
            ->get();
    }

    if ($request->ajax()) {
        return view('tabs.deliveries_table', compact('deliveries', 'acknowledgedPOs'))->render();
    }

    return view('tabs.deliveries', compact('deliveries', 'acknowledgedPOs', 'user'));
}


    /**
     * Create delivery from acknowledged PO
     */
    public function makeDelivery(Request $request, $id)
    {
        $user = Auth::user();
        $purchaseOrder = PurchaseOrder::with('items')->findOrFail($id);

        // Authorization
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

    /**
     * Vendor reports supplied quantity for a delivery item
     */
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

        // Calculate the remaining quantity for this PO line item
        $poItem = $deliveryItem->purchaseOrderItem;
        $orderedQty = $poItem->quantity;

        $totalSupplied = DeliveryItem::where('purchase_order_item_id', $poItem->id)
        ->where('id', '!=', $deliveryItem->id)
        ->sum('quantity_supplied');

        // The maximum the vendor can supply in this entry
        $maxQty = $orderedQty - $totalSupplied;
        $orderDate = $deliveryItem->delivery->purchaseOrder->order_date;

        // Custom error message
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

    /**
     * Admin verifies received quantity
     */
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

            foreach ($deliveryItemIds as $i => $id) {
                $item = \App\Models\DeliveryItem::find($id);
                if (!$item || $item->quantity_supplied > 0) continue; // Don't update if already supplied

                // Validation: prevent over-supply
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
                $item->save();
            }

             $order = \App\Models\PurchaseOrder::with(['items', 'deliveries.items'])->findOrFail($orderId);

    $html = view('tabs.delivery_order_items', ['order' => $order])->render();

    return response()->json(['html' => $html]);
        }



    /**
     * Add another partial delivery for a PO line item
     */
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
