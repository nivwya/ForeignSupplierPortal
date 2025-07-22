<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\PurchaseOrder;
use App\Models\PoMaster;

class OrderController extends Controller
{
    // Main Orders tab view
    public function index(Request $request)
    {
        $admin = auth()->user();
        if ($admin->isSuperAdmin()) {
            $companyCodes = \App\Models\AdminCompanyCode::all()->pluck('company_code');
        } else {
            $companyCodes = \App\Models\AdminCompanyCode::where('admin_email', $admin->email)
                ->pluck('company_code');
        }

        //changes by niveditha
        $query = \App\Models\PoMaster::whereIn('company_code', $companyCodes);
        $orders = $query->orderByDesc('purchase_order_dt')->paginate(20);


        return view('admin.admin_orders', ['orders' => $orders]);
    }

    public function splitView($poid)
    {
        $po = PoMaster::findOrFail($poid);
        return view('admin.split_screen_partial', compact('po'))->render();
    }

    public function releaseAll(Request $request)
    {
        // Find matching PoMaster records first
        $poMasterQuery = \App\Models\PoMaster::query();

        if ($request->filled('purchase_doc_no')) {
            $poMasterQuery->where('purchase_doc_no', 'like', '%' . $request->purchase_doc_no . '%');
        }
        if ($request->filled('company')) {
            $poMasterQuery->where('company_code', 'like', '%' . $request->company . '%');
        }
        if ($request->filled('department')) {
            $poMasterQuery->where('desc_purchase_org', 'like', '%' . $request->department . '%');
        }

        $poMasterRecords = $poMasterQuery->pluck('purchase_doc_no');

        // Now update PurchaseOrder records with matching order_number
        $query = \App\Models\PurchaseOrder::whereIn('order_number', $poMasterRecords);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
 //changes end
        // Only update orders with a PDF attached
        $count = $query->whereNotNull('po_pdf')->whereIn('status', ['not verified', 'issued'])->update(['status' => 'issued']);

        return response()->json([
            'success' => true,
            'message' => "$count order(s) released."
        ]);
    }

    // AJAX: Orders table partial (with optional filters)
   public function table(Request $request)
    {
        //changes by niveditha
        $query = PoMaster::query();

        // Apply filters as before, but on PoMaster fields
        if ($request->filled('order_number')) {
            $query->where('purchase_doc_no', 'like', '%' . $request->order_number . '%');
        }
        if ($request->filled('company')) {
            $query->where('company_code', 'like', '%' . $request->company . '%');
        }
        if ($request->filled('department')) {
            $query->where('desc_purchase_org', 'like', '%' . $request->department . '%');
        }
        // Only filter by status if set
        // (No else needed: if not set, show all statuses)

        $orders = $query->orderByDesc('purchase_order_dt')->get();

        $orders = $orders->map(function($order) {
            $po = \DB::table('purchase_orders')->where('order_number', $order->purchase_doc_no)->first();
            $order->ack_status = $po->ack_status ?? $order->ack_status;
            $order->po_pdf = $po->po_pdf ?? null;
            $order->status = $po->status ?? $order->ack_status;
            $order->id = $po->id ?? null;
            return $order;
        });

        $orders = $orders->unique('purchase_doc_no')->values();

        // If status filter is set, filter here
        if ($request->filled('status')) {
            $orders = $orders->filter(function($order) use ($request) {
                return strtolower($order->status) === strtolower($request->status);
            });
        }

        // Paginate manually (since we have a collection now)
        $perPage = 50;
        $page = $request->input('page', 1);
        $paged = $orders->slice(($page - 1) * $perPage, $perPage)->values();
        $orders = new \Illuminate\Pagination\LengthAwarePaginator($paged, $orders->count(), $perPage, $page, [
            'path' => $request->url(),
            'query' => $request->query(),
        ]);

        if ($request->ajax()) {
            return view('admin.admin_orders_table', ['orders' => $orders])->render();
        }
        //changes end
        return view('admin.admin_orders', ['orders' => $orders]);
    }

   //changes by niveditha
    public function items($orderNumber)
    {
        $order = PoMaster::where('purchase_doc_no', $orderNumber)->firstOrFail();
        $items = PoMaster::where('purchase_doc_no', $orderNumber)->get(); // If items are in same table, else adjust
        // Get PDF/status from purchase_orders
        $po = \DB::table('purchase_orders')->where('order_number', $orderNumber)->first();
        $order->ack_status = $po->ack_status ?? $order->ack_status;
        $order->po_pdf = $po->po_pdf ?? null;
        $order->status = $po->status ?? $order->ack_status;
        $order->id = $po->id ?? null;
//chnages end
        return view('admin.admin_orders_items', compact('order', 'items'))->render();
    }

    // AJAX: PO upload (PDF)
    public function attachPO(Request $request)
    {
        $request->validate([
            'po_file' => 'required|file|mimes:pdf|max:10240',
        ]);

        $file = $request->file('po_file');
        $filename = $file->getClientOriginalName();

        $path = $file->storeAs('purchase_orders/tmp', $filename, 'public');

        //changes made by niveditha
        $poNumber = pathinfo($filename, PATHINFO_FILENAME); // Remove .pdf

        
        $poMaster = \App\Models\PoMaster::where('purchase_doc_no', $poNumber)->first();
        if (!$poMaster) {
        
        //changes end
            return response()->json([
                'success' => false,
                'message' => "Could not extract PO number from filename. Please ensure the file is named exactly as the PO number (e.g., 4500001234.pdf)",
            ], 422);
        }

        // Find PO in DB
        $po = \App\Models\PurchaseOrder::with('items')->where('order_number', $poNumber)->first();
        if (!$po) {
            return response()->json([
                'success' => false,
                'message' => "PO not found: $poNumber"
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => "PO found. Ready for verification.",
            'po' => $po,
            'items' => $po->items,
            'pdf_url' => \Storage::url($path),
            'tmp_pdf_path' => $path,
        ]);
    }

    // AJAX: Confirm PO attachment after verification
    public function confirmPOAttachment(Request $request)
{
    $request->validate([
        'po_id' => 'required|exists:purchase_orders,id',
        'tmp_pdf_path' => 'required|string',
    ]);

    $user = auth()->user();

    if ($user->is_superadmin) {
        $po = PurchaseOrder::findOrFail($request->po_id);
    } else {
        // Restrict access by allowed company codes
        $allowedCompanyCodes = \App\Models\AdminCompanyCode::where('admin_email', $user->email)->pluck('company_code');
        $po = PurchaseOrder::where('id', $request->po_id)
            ->whereIn('amg_company_code', $allowedCompanyCodes)
            ->first();

        if (!$po) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to update this PO.',
            ], 403);
        }
    }

    $filename = basename($request->tmp_pdf_path);
    $newPath = 'purchase_orders/' . $filename;

    // Overwrite if file already exists
    if (Storage::disk('public')->exists($newPath)) {
        Storage::disk('public')->delete($newPath);
    }

    $moveSuccess = Storage::disk('public')->move($request->tmp_pdf_path, $newPath);

    if (!$moveSuccess) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to move the PDF file!',
        ], 500);
    }

    $po->po_pdf = $newPath;
    $po->status = 'not verified';
    $po->save();

    return response()->json([
        'success' => true,
        'message' => 'PO PDF attached and saved as not verified!',
    ]);
}

    // AJAX: Remove attached PDF from PO
    public function removePOTemp(Request $request)
    {
        $request->validate([
            'po_id' => 'required|integer|exists:purchase_orders,id',
        ]);

        $po = PurchaseOrder::find($request->po_id);

        if (!$po) {
            return response()->json([
                'success' => false,
                'message' => 'Purchase Order not found.',
            ], 404);
        }

        // Optionally, delete the PDF file from storage if needed
        if ($po->po_pdf) {
            $pdfPath = storage_path('app/public/' . $po->po_pdf);
            if (file_exists($pdfPath)) {
                unlink($pdfPath);
            }
        }

        // Remove the PDF reference from the database
        $po->po_pdf = null;
        $po->save();

        return response()->json([
            'success' => true,
            'message' => 'Pdf removed successfully.',
        ]);
    }

    // AJAX: Issue a single PO (set status to issued)
    public function issuePO(Request $request)
    {
        $request->validate([
            'po_id' => 'required|exists:purchase_orders,id',
        ]);
        $po = PurchaseOrder::findOrFail($request->po_id);

        if (!$po->po_pdf || !Storage::disk('public')->exists($po->po_pdf)) {
            return response()->json([
                'success' => false,
                'message' => 'No PDF attached to PO.',
            ], 400);
        }
        $po->status = 'issued';
        $po->save();

        return response()->json([
            'success' => true,
            'message' => 'PO released successfully!',
        ]);
    }
    public function issueAllPOs(Request $request)
    {
        $pos = PurchaseOrder::where('status', 'not verified')
            ->whereNotNull('po_pdf')
            ->get();

        $count = 0;
        foreach ($pos as $po) {
            if ($po->status === 'acknowledged' || $po->status === 'partial delivery' || $po->status === 'delivered') {
            return response()->json([
                'success' => false,
                'message' => "PO $po->order_number already released to Vendor",
            ], 400);
            }
            if ($po->po_pdf && Storage::disk('public')->exists($po->po_pdf)) {
                $po->status = 'issued';
                $po->save();
                $count++;
            }

        }
        return response()->json([
            'success' => true,
            'message' => "$count POs released.",
        ]);
    }
    public function AttachPoPdftoRow(Request $request)
    {
        $request->validate([
            'po_file' => 'required|file|mimes:pdf|max:10240',
            'po_id'   => 'required|exists:purchase_orders,id',
        ]);

        $po = PurchaseOrder::findOrFail($request->po_id);
        $file = $request->file('po_file');

        // Get original file name
        $fileName = $file->getClientOriginalName();
        $expectedPrefix =  $po->order_number;

        // Check if file name starts with expected prefix
        if (!Str::startsWith($fileName, $expectedPrefix)) {
            return response()->json([
                'success' => false,
                'message' => 'Uploaded file name does not match expected PO file name format (should start with ' . $expectedPrefix . ').'
            ], 422);
        }

        // Continue with your logic
        $filename = $expectedPrefix . '-' . time() . '.pdf';
        $path = $file->storeAs('purchase_orders', $filename, 'public');

        if ($po->po_pdf && Storage::disk('public')->exists($po->po_pdf)) {
            Storage::disk('public')->delete($po->po_pdf);
        }

        $po->po_pdf = $path;
        $po->status = 'not verified';
        $po->save();

        return response()->json([
            'success' => true,
            'message' => 'PO PDF attached to this PO!',
        ]);
    }
    public function RemovePdffromRow(Request $request)
    {
         //changes by niveditha     
        $po = null;
        if ($request->filled('po_id')) {
            $po = PurchaseOrder::find($request->po_id);
        } elseif ($request->filled('po_number')) {
            $po = PurchaseOrder::where('order_number', $request->po_number)->first();
        }
        //changes end
        if (!$po) {
            return response()->json([
                'success' => false,
                'message' => 'Purchase Order not found.',
            ], 404);
        }
        if ($po->status === 'acknowledged' || $po->status === 'partial delivery' || $po->status === 'delivered') {
            return response()->json([
                'success' => false,
                'message' => 'PO already released to Vendor',
            ], 400);
        }
        if ($po->po_pdf && Storage::disk('public')->exists($po->po_pdf)) {
            Storage::disk('public')->delete($po->po_pdf);
        }
        $po->po_pdf = null;
        $po->save();
        return response()->json([
            'success' => true,
            'message' => 'PDF removed from this PO!',
        ]);
    }

    public function IssuePoRow(Request $request)
    {
        $request->validate([
            'po_id' => 'required|exists:purchase_orders,id',
        ]);
        $po = PurchaseOrder::findOrFail($request->po_id);
        if (!$po->po_pdf || !Storage::disk('public')->exists($po->po_pdf)) {
            return response()->json([
                'success' => false,
                'message' => 'No PDF attached to PO.',
            ], 400);
        }
        if ($po->status === 'acknowledged' || $po->status === 'partial delivery' || $po->status === 'delivered') {
            return response()->json([
                'success' => false,
                'message' => 'PO already released to Vendor',
            ], 400);
        }
        $po->status = 'issued';
        $po->save();
        return response()->json([
            'success' => true,
            'message' => 'PO released successfully!',
        ]);
    }


}
