<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Invoice;
use App\Models\VendorContact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    /**
     * Display a listing of payments
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        if ($user->hasRole('admin')) {
            // Admin sees all payments
            $payments = Payment::with(['invoice', 'invoice.purchaseOrder', 'invoice.purchaseOrder.vendor'])->get();
        } else {
            // Vendor sees only their payments
            $email = $user->email;
            $vendorContact = VendorContact::where('email', $email)->first();
            
            if (!$vendorContact) {
                return response()->json(['success' => false, 'message' => 'No vendor associated with this email'], 404);
            }
            
            $payments = Payment::whereHas('invoice.purchaseOrder', function($query) use ($vendorContact) {
                $query->where('vendor_id', $vendorContact->vendor_id);
            })->with(['invoice', 'invoice.purchaseOrder'])->get();
        }
        
        return response()->json([
            'success' => true,
            'data' => $payments
        ]);
    }

    /**
     * Process payment for an invoice (admin only)
     */
    public function store(Request $request)
    {
        $user = $request->user();
        if (!$user->hasRole('admin')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        $validated = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'reference_number' => 'nullable|string|max:100',
            'receipt_pdf' => 'required|file|mimes:pdf',
            'notes' => 'nullable|string',
        ]);
        
        // Store receipt PDF
        $receiptPdfPath = $request->file('receipt_pdf')->store('receipts');
        
        // Create payment
        $payment = Payment::create([
            'invoice_id' => $validated['invoice_id'],
            'amount' => $validated['amount'],
            'payment_date' => $validated['payment_date'],
            'reference_number' => $validated['reference_number'] ?? null,
            'status' => 'paid',
            'notes' => $validated['notes'] ?? null,
            'receipt_pdf' => $receiptPdfPath,
        ]);
        
        // Update invoice paid/due amounts
        $invoice = $payment->invoice;
        $invoice->amount_paid += $validated['amount'];
        $invoice->amount_due = max(0, $invoice->invoice_amount - $invoice->amount_paid);
        $invoice->status = $invoice->amount_due <= 0 ? 'paid' : 'partially_paid';
        $invoice->save();
        
        // If invoice is fully paid, update PO status
        if ($invoice->status === 'paid') {
            $invoice->purchaseOrder->update(['status' => 'completed']);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Payment processed successfully',
            'data' => $payment
        ], 201);
    }

    /**
     * Display the specified payment
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        $payment = Payment::with(['invoice', 'invoice.purchaseOrder', 'invoice.purchaseOrder.vendor'])->findOrFail($id);
        
        if (!$user->hasRole('admin')) {
            // Check if user is associated with the vendor
            $email = $user->email;
            $vendorContact = VendorContact::where('email', $email)
                ->where('vendor_id', $payment->invoice->purchaseOrder->vendor_id)
                ->first();
                
            if (!$vendorContact) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
        }
        
        return response()->json([
            'success' => true,
            'data' => $payment
        ]);
    }

    /**
     * Download Payment Receipt PDF
     */
    public function downloadReceipt(Request $request, $id)
    {
        $user = $request->user();
        $payment = Payment::with(['invoice', 'invoice.purchaseOrder'])->findOrFail($id);
        
        if (!$user->hasRole('admin')) {
            // Check if user is associated with the vendor
            $email = $user->email;
            $vendorContact = VendorContact::where('email', $email)
                ->where('vendor_id', $payment->invoice->purchaseOrder->vendor_id)
                ->first();
                
            if (!$vendorContact) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
        }
        
        if (!$payment->receipt_pdf) {
            return response()->json(['success' => false, 'message' => 'Receipt PDF not found'], 404);
        }
        
        return Storage::download($payment->receipt_pdf);
    }
}
