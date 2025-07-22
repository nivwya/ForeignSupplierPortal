<?php

namespace App\Http\Controllers;

use App\Models\VendorBank;
use App\Models\VendorContact;
use App\Http\Requests\StoreVendorBankRequest;
use Illuminate\Http\Request;

class VendorBankController extends Controller
{
    // List all bank accounts for the authenticated user's vendor(s)
    public function index(Request $request)
    {
        $email = $request->user()->email;
        $vendorContact = VendorContact::where('email', $email)->first();

        if (!$vendorContact) {
            return response()->json(['success' => false, 'message' => 'No vendor associated with this email'], 404);
        }

        $banks = VendorBank::where('vendor_id', $vendorContact->vendor_id)->get();

        return response()->json([
            'success' => true,
            'data' => $banks
        ]);
    }

    // Store new bank account (only for the authenticated user's vendor)
    public function store(StoreVendorBankRequest $request)
    {
        $email = $request->user()->email;
        $vendorContact = VendorContact::where('email', $email)->first();

        if (!$vendorContact || $vendorContact->vendor_id != $request->vendor_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validated();

        // Optional: Encrypt sensitive fields if needed
        // $validated['bank_account'] = Crypt::encrypt($validated['bank_account']);

        $bank = VendorBank::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Bank account created',
            'data' => $bank
        ], 201);
    }

    // Show a single bank account (only if the user is associated with the vendor)
    public function show(Request $request, VendorBank $bank)
    {
        $email = $request->user()->email;
        $vendorContact = VendorContact::where('email', $email)
            ->where('vendor_id', $bank->vendor_id)
            ->first();

        if (!$vendorContact) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $bank
        ]);
    }

    // Update a bank account (only if the user is associated with the vendor)
    public function update(StoreVendorBankRequest $request, VendorBank $bank)
    {
        $email = $request->user()->email;
        $vendorContact = VendorContact::where('email', $email)
            ->where('vendor_id', $bank->vendor_id)
            ->first();

        if (!$vendorContact) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validated();

        // Optional: Encrypt sensitive fields if needed
        // $validated['bank_account'] = Crypt::encrypt($validated['bank_account']);

        $bank->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Bank account updated',
            'data' => $bank
        ]);
    }

    // Delete a bank account (only if the user is associated with the vendor)
    public function destroy(Request $request, VendorBank $bank)
    {
        $email = $request->user()->email;
        $vendorContact = VendorContact::where('email', $email)
            ->where('vendor_id', $bank->vendor_id)
            ->first();

        if (!$vendorContact) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $bank->delete();

        return response()->json([
            'success' => true,
            'message' => 'Bank account deleted'
        ]);
    }
}


