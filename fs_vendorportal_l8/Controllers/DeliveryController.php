<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\DeliveryItem;
use App\Models\PurchaseOrder;
use App\Models\VendorContact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DeliveryController extends Controller
{

    public function deliveriesTab(Request $request)
{
    $user = auth()->user();

    // Existing deliveries
    if ($user->hasRole('admin')) {
        $deliveries = \App\Models\Delivery::with(['purchaseOrder', 'items'])->get();
        $acknowledgedPOs = \App\Models\PurchaseOrder::where('status', 'acknowledged')
            ->doesntHave('deliveries')
            ->get();
    } else {
        $vendorContact = \App\Models\VendorContact::where('email', $user->email)->first();
        $deliveries = [];
        $acknowledgedPOs = [];
        if ($vendorContact) {
            $deliveries = \App\Models\Delivery::whereHas('purchaseOrder', function($q) use ($vendorContact) {
                $q->where('vendor_id', $vendorContact->vendor_id);
            })->with(['purchaseOrder', 'items'])->get();

            $acknowledgedPOs = \App\Models\PurchaseOrder::where('status', 'acknowledged')
                ->where('vendor_id', $vendorContact->vendor_id)
                ->doesntHave('deliveries')
                ->get();
        }
    }

    return view('tabs.deliveries', [
        'deliveries' => $deliveries,
        'acknowledgedPOs' => $acknowledgedPOs,
        'user' => $user
    ]);
}

    /**
     * Display a listing of deliveries
     */

    public function index(Request $request)
    {
        $user = $request->user();
        
        if ($user->hasRole('admin')) {
            // Admin sees all deliveries
            $deliveries = Delivery::with(['purchaseOrder', 'purchaseOrder.vendor'])->get();
        } else {
            // Vendor sees only their deliveries
            $email = $user->email;
            $vendorContact = VendorContact::where('email', $email)->first();
            
            if (!$vendorContact) {
                return response()->json(['success' => false, 'message' => 'No vendor associated with this email'], 404);
            }
            
            $deliveries = Delivery::whereHas('purchaseOrder', function($query) use ($vendorContact) {
                $query->where('vendor_id', $vendorContact->vendor_id);
            })->with('purchaseOrder')->get();
        }
        
        return response()->json([
            'success' => true,
            'data' => $deliveries
        ]);
    }

    /**
     * Display the specified delivery
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        $delivery = Delivery::with(['purchaseOrder', 'purchaseOrder.vendor'])->findOrFail($id);
        
        if (!$user->hasRole('admin')) {
            // Check if user is associated with the vendor
            $email = $user->email;
            $vendorContact = VendorContact::where('email', $email)
                ->where('vendor_id', $delivery->purchaseOrder->vendor_id)
                ->first();
                
            if (!$vendorContact) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
        }
        
        return response()->json([
            'success' => true,
            'data' => $delivery
        ]);
    }

    /**
     * Download GRN PDF
     */
    public function downloadGrn(Request $request, $id)
    {
        $user = $request->user();
        $delivery = Delivery::with('purchaseOrder')->findOrFail($id);
        
        if (!$user->hasRole('admin')) {
            // Check if user is associated with the vendor
            $email = $user->email;
            $vendorContact = VendorContact::where('email', $email)
                ->where('vendor_id', $delivery->purchaseOrder->vendor_id)
                ->first();
                
            if (!$vendorContact) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
        }
        
        if (!$delivery->grn_pdf) {
            return response()->json(['success' => false, 'message' => 'GRN PDF not found'], 404);
        }
        
        return Storage::download($delivery->grn_pdf);
    }

    public function reportSuppliedQuantity(Request $request, $id)
{
    $user = Auth::user();
    $deliveryItem = DeliveryItem::with('delivery.purchaseOrder')->findOrFail($id);

    $vendorContact = VendorContact::where('email', $user->email)
        ->where('vendor_id', $deliveryItem->delivery->purchaseOrder->vendor_id)
        ->first();

    if (!$vendorContact) {
        return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
    }

    $validated = $request->validate([
        'quantity_supplied' => 'required|numeric|min:0|max:' . $deliveryItem->quantity
    ]);

    $deliveryItem->update([
        'quantity_supplied' => $validated['quantity_supplied'],
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
                'qty_received_by_amg' => 'required|numeric|min:0'
            ]);

            $deliveryItem->update([
                'qty_received_by_amg' => $validated['qty_received_by_amg'],
                'amg_received_date' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Quantity received verified successfully'
            ]);
        }
}
