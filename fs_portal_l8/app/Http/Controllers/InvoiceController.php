<?php
namespace App\Http\Controllers;
use App\Models\Invoice;
use App\Models\PurchaseOrder;
use App\Models\VendorContact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Delivery;
use App\Models\DeliveryItem;
use Illuminate\Support\Facades\Auth;
class InvoiceTabController extends Controller
{
    public function upload(Request $request, $id)
{
    $user = $request->user();
    $invoice = Invoice::findOrFail($id);
    if (!$user->hasRole('admin')) {
        $vendorContact = \App\Models\VendorContact::where('email', $user->email)
            ->where('vendor_id', $invoice->purchaseOrder->vendor_id)
            ->first();
        if (!$vendorContact) {
            return back()->with('error', 'Unauthorized');
        }
    }
    $validated = $request->validate([
        'invoice_pdf' => 'required|file|mimes:pdf|max:10240',
        'invoice_number' => 'required|string',
        'invoice_date' => 'required|date',
        'amount' => 'required|numeric|min:1',
    ]);
    $pdfPath = $request->file('invoice_pdf')->store('invoices');
    $invoice->update([
        'invoice_pdf' => $pdfPath,
        'invoice_number' => $validated['invoice_number'],
        'invoice_date' => $validated['invoice_date'],
        'amount' => $validated['amount'],
        'status' => 'submitted',
    ]);
    return back()->with('success', 'Invoice uploaded successfully.');
}
    public function invoicesTab(Request $request)
    {
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            $invoices = Invoice::with([
                'purchaseOrder.vendor',
                'delivery'
            ])->get();
        } else {
            $email = $user->email;
            $vendorContact = VendorContact::where('email', $email)->first();

            if (!$vendorContact) {
                return view('tabs.invoices', [
                    'invoices' => collect(),
                    'error' => 'No vendor associated with this email'
                ]);
            }
            $invoices = Invoice::whereHas('purchaseOrder', function($query) use ($vendorContact) {
                $query->where('vendor_id', $vendorContact->vendor_id);
            })->with([
                'purchaseOrder.vendor',
                'delivery'
            ])->get();
        }
        return view('tabs.invoices', [
            'invoices' => $invoices,
            'user' => $user
        ]);
    }
    public function downloadInvoicePdf($id)
    {
        $user = Auth::user();
        $invoice = Invoice::with('purchaseOrder')->findOrFail($id);
        if (!$user->hasRole('admin')) {
            $email = $user->email;
            $vendorContact = VendorContact::where('email', $email)
                ->where('vendor_id', $invoice->purchaseOrder->vendor_id)
                ->first();
            if (!$vendorContact) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
        }
        if (!$invoice->invoice_pdf || !\Storage::exists($invoice->invoice_pdf)) {
            return response()->json(['success' => false, 'message' => 'Invoice PDF not found'], 404);
        }
        $filename = 'invoice_' . $invoice->invoice_number . '.pdf';
        return \Storage::download($invoice->invoice_pdf, $filename);
    }
    public function index(Request $request)
    {
        $user = $request->user();
        if ($user->hasRole('admin')) {
            $invoices = Invoice::with(['purchaseOrder', 'purchaseOrder.vendor'])->get();
        } else {
            $email = $user->email;
            $vendorContact = VendorContact::where('email', $email)->first();
            if (!$vendorContact) {
                return response()->json(['success' => false, 'message' => 'No vendor associated with this email'], 404);
            }
            $invoices = Invoice::whereHas('purchaseOrder', function($query) use ($vendorContact) {
                $query->where('vendor_id', $vendorContact->vendor_id);
            })->with('purchaseOrder')->get();
        }
        return response()->json([
            'success' => true,
            'data' => $invoices
        ]);
    }
    public function store(Request $request)
    {
        $user = $request->user();
        $validated = $request->validate([
            'order_id' => 'required|exists:purchase_orders,id',
            'invoice_number' => 'required|string|unique:invoices',
            'invoice_date' => 'required|date',
            'invoice_amount' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'due_date' => 'required|date|after:invoice_date',
            'payment_terms' => 'required|string',
            'invoice_pdf' => 'required|file|',
        ]);
        $purchaseOrder = PurchaseOrder::findOrFail($validated['order_id']);
        if ($purchaseOrder->status !== 'grn_issued') {
            return response()->json([
                'success' => false, 
                'message' => 'Purchase order must have GRN issued before submitting invoice'
            ], 400);
        }
        if (!$user->hasRole('admin')) {
            $email = $user->email;
            $vendorContact = VendorContact::where('email', $email)
                ->where('vendor_id', $purchaseOrder->vendor_id)
                ->first();
            if (!$vendorContact) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
        }
        $invoicePdfPath = $request->file('invoice_pdf')->store('invoices');
        $invoice = Invoice::create([
            'order_id' => $validated['order_id'],
            'invoice_number' => $validated['invoice_number'],
            'invoice_date' => $validated['invoice_date'],
            'invoice_amount' => $validated['invoice_amount'],
            'amount_paid' => 0,
            'amount_due' => $validated['invoice_amount'],
            'currency' => $validated['currency'],
            'status' => 'submitted',
            'due_date' => $validated['due_date'],
            'payment_terms' => $validated['payment_terms'],
            'invoice_pdf' => $invoicePdfPath,
        ]);
        $purchaseOrder->update(['status' => 'invoice_submitted']);
        return response()->json([
            'success' => true,
            'message' => 'Invoice submitted successfully',
            'data' => $invoice
        ], 201);
    }
    public function show(Request $request, $id)
    {
        $user = $request->user();
        $invoice = Invoice::with(['purchaseOrder', 'purchaseOrder.vendor', 'payments'])->findOrFail($id);
        
        if (!$user->hasRole('admin')) {
            // Check if user is associated with the vendor
            $email = $user->email;
            $vendorContact = VendorContact::where('email', $email)
                ->where('vendor_id', $invoice->purchaseOrder->vendor_id)
                ->first();
                
            if (!$vendorContact) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
        }
        return response()->json([
            'success' => true,
            'data' => $invoice
        ]);
    }
    public function downloadPdf(Request $request, $id)
    {
        $user = $request->user();
        $invoice = Invoice::with('purchaseOrder')->findOrFail($id);
        
        if (!$user->hasRole('admin')) {
            // Check if user is associated with the vendor
            $email = $user->email;
            $vendorContact = VendorContact::where('email', $email)
                ->where('vendor_id', $invoice->purchaseOrder->vendor_id)
                ->first();
                
            if (!$vendorContact) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
        }
        if (!$invoice->invoice_pdf) {
            return response()->json(['success' => false, 'message' => 'Invoice PDF not found'], 404);
        }
        return Storage::download($invoice->invoice_pdf);
    }
}
