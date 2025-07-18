<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Vendor;
use App\Models\VendorContact;
use App\Models\Delivery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class OrderTabController extends Controller
{
    public function ordersTab(Request $request)
    {
        $user = auth()->user();
        $query = \App\Models\PurchaseOrder::query();

        if ($user->hasRole('admin')) {
            // Admin sees all
        } else {
            $email = $user->email;
            $vendorContact = \App\Models\VendorContact::where('email', $email)->first();
            if ($vendorContact) {
                $query->where('vendor_id', $vendorContact->vendor_id);
            } else {
                $orders = collect();
                if ($request->ajax()) {
                    return view('tabs.orders_table', compact('orders'))->render();
                }
                return view('tabs.orders', compact('orders'));
            }
        }

        // Apply filters
        if ($request->filled('order_number')) {
            $query->where('order_number', 'like', '%' . $request->order_number . '%');
        }
        if ($request->filled('company')) {
            $query->where('company', 'like', '%' . $request->company . '%');
        }
        if ($request->filled('department')) {
            $query->where('department', 'like', '%' . $request->department . '%');
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->with(['items'])->orderByDesc('order_date')->get();

        if ($request->ajax()) {
            return view('tabs.orders_table', compact('orders'))->render();
        }

        return view('tabs.orders', compact('orders'));
    }
}
