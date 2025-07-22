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
use App\Models\PoMaster;

class OrderTabController extends Controller
{
    public function ordersTab(Request $request)
    {
        $user = auth()->user();
        $query = PoMaster::query();
        $email = $user->email;
        $vendorContact = \App\Models\VendorContact::where('email', $email)->first();
        if ($vendorContact) {
            $query->where('vendor_account_no', $vendorContact->vendor_id);
        } else {
            $orders = collect();
            if ($request->ajax()) {
                return view('tabs.orders_table', compact('orders'))->render();
            }
            return view('tabs.orders', compact('orders'));
        }
        if ($request->filled('order_number')) {
            $query->where('purchase_doc_no', 'like', '%' . $request->order_number . '%');
        }
        if ($request->filled('company')) {
            $query->where('company_code', 'like', '%' . $request->company . '%');
        }
        if ($request->filled('department')) {
            $query->where('desc_purchase_org', 'like', '%' . $request->department . '%');
        }
        if ($request->filled('status')) {
            // We'll filter by ack_status after merging with purchase_orders
        }
        $orders = $query->orderByDesc('purchase_order_dt')->get();
        $orders = $orders->map(function($order) {
            $po = \DB::table('purchase_orders')->where('order_number', $order->purchase_doc_no)->first();
            $order->ack_status = $po->ack_status ?? $order->ack_status;
            $order->po_pdf = $po->po_pdf ?? null;
            $order->status = $po->status ?? $order->ack_status;
            $order->id = $po->id ?? null;
            return $order;
        });
        if ($request->filled('status')) {
            $orders = $orders->filter(function($order) use ($request) {
                return strtolower($order->status) === strtolower($request->status);
            });
        }
        $perPage = 50;
        $page = $request->input('page', 1);
        $paged = $orders->slice(($page - 1) * $perPage, $perPage)->values();
        $orders = new \Illuminate\Pagination\LengthAwarePaginator($paged, $orders->count(), $perPage, $page, [
            'path' => $request->url(),
            'query' => $request->query(),
        ]);
        if ($request->ajax()) {
            return view('tabs.orders_table', ['orders' => $orders])->render();
        }
        return view('tabs.orders', ['orders' => $orders]);
    }
    public function OrderItems($orderNumber)
    {
        $order = PoMaster::where('purchase_doc_no', $orderNumber)->firstOrFail();
        $items = PoMaster::where('purchase_doc_no', $orderNumber)->get(); // If items are in same table, else adjust
        $po = \DB::table('purchase_orders')->where('order_number', $orderNumber)->first();
        $order->ack_status = $po->ack_status ?? $order->ack_status;
        $order->po_pdf = $po->po_pdf ?? null;
        $order->status = $po->status ?? $order->ack_status;
        $order->id = $po->id ?? null;
        return view('tabs.order_items', [
            'order' => $order,
            'items' => $items,
        ]);
    }
}
