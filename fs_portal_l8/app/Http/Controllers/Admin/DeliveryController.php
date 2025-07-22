<?php

namespace App\Http\Controllers\Admin;

use App\Models\Delivery;
use App\Models\DeliveryItem;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\VendorContact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class DeliveryController extends Controller
{
    public function deliveryOrderItems($orderId)
    {
        $user = auth()->user();

        // Load order with items and deliveries
        $order = PurchaseOrder::with(['items', 'deliveries.items'])->findOrFail($orderId);

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
            if ($user->is_superadmin) {
                $query->with(['purchaseOrder.vendor', 'items.purchaseOrderItem']);
            } else {
                $query->whereHas('purchaseOrder', function ($q) use ($user) {
                    $companyCodes = \App\Models\AdminCompanyCode::where('admin_email', $user->email)
                        ->pluck('company_code');
                    $q->whereIn('amg_company_code', $companyCodes);
                })->with(['purchaseOrder.vendor', 'items.purchaseOrderItem']);
            }
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

            $query->whereHas('purchaseOrder', function ($q) use ($vendorContact) {
                $q->where('vendor_id', $vendorContact->vendor_id);
            })->with(['purchaseOrder.vendor', 'items.purchaseOrderItem']);
        }

        // 🔍 Filters
        if ($request->filled('order_number')) {
            $query->whereHas('purchaseOrder', function ($q) use ($request) {
                $q->where('order_number', 'like', '%' . $request->order_number . '%');
            });
        }

        if ($request->filled('company')) {
            $query->whereHas('purchaseOrder', function ($q) use ($request) {
                $q->where('company', 'like', '%' . $request->company . '%');
            });
        }

        if ($request->filled('department')) {
            $query->whereHas('purchaseOrder', function ($q) use ($request) {
                $q->where('department', 'like', '%' . $request->department . '%');
            });
        }

        if ($request->filled('status')) {
            $query->whereHas('purchaseOrder', function ($q) use ($request) {
                $q->where('status', $request->status);
            });
        }

        $deliveries = $query->get();

        foreach ($deliveries as $delivery) {
            foreach ($delivery->items as $item) {
                $item->total_supplied = DeliveryItem::where('purchase_order_item_id', $item->purchase_order_item_id)
                    ->sum('quantity_supplied');
                $item->order_quantity = $item->purchaseOrderItem->quantity;
            }
        }
        if ($user->hasRole('admin')) {
            if ($user->is_superadmin) {
                $acknowledgedPOs = PurchaseOrder::where('status', 'acknowledged')
                    ->whereDoesntHave('deliveries')
                    ->with(['vendor', 'items'])
                    ->get();
            } else {
                $companyCodes = \App\Models\AdminCompanyCode::where('admin_email', $user->email)->pluck('company_code');
                $acknowledgedPOs = PurchaseOrder::where('status', 'acknowledged')
                    ->whereIn('amg_company_code', $companyCodes)
                    ->whereDoesntHave('deliveries')
                    ->with(['vendor', 'items'])
                    ->get();
            }
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
}