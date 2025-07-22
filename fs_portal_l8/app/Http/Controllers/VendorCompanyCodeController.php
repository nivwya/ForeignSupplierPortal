<?php

namespace App\Http\Controllers;

use App\Models\VendorCompanyCode;
use App\Models\VendorContact;
use Illuminate\Http\Request;

class VendorCompanyCodeController extends Controller
{
    // List all company codes for the authenticated user's vendor(s)
    public function index(Request $request)
    {
        $email = $request->user()->email;
        $vendorContact = VendorContact::where('email', $email)->first();

        if (!$vendorContact) {
            return response()->json(['success' => false, 'message' => 'No vendor associated with this email'], 404);
        }

        $companyCodes = VendorCompanyCode::where('vendor_id', $vendorContact->vendor_id)->get();

        return response()->json([
            'success' => true,
            'data' => $companyCodes
        ]);
    }

    // Store a new company code (only for the authenticated user's vendor)
    public function store(Request $request)
    {
        $email = $request->user()->email;
        $vendorContact = VendorContact::where('email', $email)->first();

        if (!$vendorContact || $vendorContact->vendor_id != $request->vendor_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'company_code' => 'required|string|max:20',
            'account_number' => 'required|string|max:50',
            'reconciliation_account' => 'required|string|max:50',
            'payment_term' => 'required|string|max:20',
            'payment_block' => 'required|string|max:20',
            'head_office_account_number' => 'required|string|max:50'
        ]);

        $companyCode = VendorCompanyCode::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Company code created',
            'data' => $companyCode
        ], 201);
    }

    // Show a single company code (only if the user is associated with the vendor)
    public function show(Request $request, VendorCompanyCode $vendor_company_code)
    {
        $email = $request->user()->email;
        $vendorContact = VendorContact::where('email', $email)
            ->where('vendor_id', $vendor_company_code->vendor_id)
            ->first();

        if (!$vendorContact) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $vendor_company_code
        ]);
    }

    // Update a company code (only if the user is associated with the vendor)
    public function update(Request $request, VendorCompanyCode $vendor_company_code)
    {
        $email = $request->user()->email;
        $vendorContact = VendorContact::where('email', $email)
            ->where('vendor_id', $vendor_company_code->vendor_id)
            ->first();

        if (!$vendorContact) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'company_code' => 'string|max:20',
            'account_number' => 'string|max:50',
            'reconciliation_account' => 'string|max:50',
            'payment_term' => 'string|max:20',
            'payment_block' => 'string|max:20',
            'head_office_account_number' => 'string|max:50'
        ]);

        $vendor_company_code->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Company code updated',
            'data' => $vendor_company_code
        ]);
    }

    // Delete a company code (only if the user is associated with the vendor)
    public function destroy(Request $request, VendorCompanyCode $vendor_company_code)
    {
        $email = $request->user()->email;
        $vendorContact = VendorContact::where('email', $email)
            ->where('vendor_id', $vendor_company_code->vendor_id)
            ->first();

        if (!$vendorContact) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $vendor_company_code->delete();

        return response()->json([
            'success' => true,
            'message' => 'Company code deleted'
        ]);
    }
}
