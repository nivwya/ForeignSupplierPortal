<?php

namespace App\Http\Controllers;

use App\Models\VendorBusinessDetail;
use App\Models\VendorContact;
use Illuminate\Http\Request;

class VendorBusinessDetailController extends Controller
{
    // List all business details for the authenticated user's vendor(s)
    public function index(Request $request)
    {
        $email = $request->user()->email;
        $vendorContact = VendorContact::where('email', $email)->first();

        if (!$vendorContact) {
            return response()->json(['success' => false, 'message' => 'No vendor associated with this email'], 404);
        }

        $businessDetails = VendorBusinessDetail::where('vendor_id', $vendorContact->vendor_id)->get();

        return response()->json([
            'success' => true,
            'data' => $businessDetails
        ]);
    }

    // Store new business detail (only for the authenticated user's vendor)
    public function store(Request $request)
    {
        $email = $request->user()->email;
        $vendorContact = VendorContact::where('email', $email)->first();

        if (!$vendorContact || $vendorContact->vendor_id != $request->vendor_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'supplier_type' => 'required|string|max:50',
            'supplier_status' => 'required|string|max:50',
            'supplier_classification' => 'required|string|max:50',
            'supplier_category' => 'required|string|max:100',
            'payment_terms' => 'required|string|max:50',
            'currency' => 'required|string|max:10',
            'tax_number' => 'required|string|max:30',
            'vat_number' => 'required|string|max:30',
            'registration_number' => 'required|string|max:30',
            'license_number' => 'required|string|max:30',
            'license_expiry' => 'required|date',
            'website' => 'nullable|string|max:255',
            'remarks' => 'nullable|string|max:255'
        ]);

        $businessDetail = VendorBusinessDetail::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Business detail created',
            'data' => $businessDetail
        ], 201);
    }

    // Show a single business detail (only if the user is associated with the vendor)
    public function show(Request $request, VendorBusinessDetail $vendor_business_detail)
    {
        $email = $request->user()->email;
        $vendorContact = VendorContact::where('email', $email)
            ->where('vendor_id', $vendor_business_detail->vendor_id)
            ->first();

        if (!$vendorContact) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $vendor_business_detail
        ]);
    }

    // Update a business detail (only if the user is associated with the vendor)
    public function update(Request $request, VendorBusinessDetail $vendor_business_detail)
    {
        $email = $request->user()->email;
        $vendorContact = VendorContact::where('email', $email)
            ->where('vendor_id', $vendor_business_detail->vendor_id)
            ->first();

        if (!$vendorContact) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'supplier_type' => 'string|max:50',
            'supplier_status' => 'string|max:50',
            'supplier_classification' => 'string|max:50',
            'supplier_category' => 'string|max:100',
            'payment_terms' => 'string|max:50',
            'currency' => 'string|max:10',
            'tax_number' => 'string|max:30',
            'vat_number' => 'string|max:30',
            'registration_number' => 'string|max:30',
            'license_number' => 'string|max:30',
            'license_expiry' => 'date',
            'website' => 'nullable|string|max:255',
            'remarks' => 'nullable|string|max:255'
        ]);

        $vendor_business_detail->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Business detail updated',
            'data' => $vendor_business_detail
        ]);
    }

    // Delete a business detail (only if the user is associated with the vendor)
    public function destroy(Request $request, VendorBusinessDetail $vendor_business_detail)
    {
        $email = $request->user()->email;
        $vendorContact = VendorContact::where('email', $email)
            ->where('vendor_id', $vendor_business_detail->vendor_id)
            ->first();

        if (!$vendorContact) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $vendor_business_detail->delete();

        return response()->json([
            'success' => true,
            'message' => 'Business detail deleted'
        ]);
    }
}
