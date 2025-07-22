<?php

namespace App\Http\Controllers;

use App\Models\VendorAddress;
use App\Models\VendorContact;
use Illuminate\Http\Request;

class VendorAddressController extends Controller
{
    // List all addresses for the authenticated user's vendor(s)
    public function index(Request $request)
    {
        $user = $request->user();
        if ($user->hasRole('admin')) {
            // Admin: allow access to all records, skip vendor email check
        $addresses = \App\Models\VendorAddress::with('vendor')->get();
        return response()->json([
            'message' => 'ADMIN CHECK-DSIPLAY',
            'adresses' => $addresses
        ], 201);
        } else {

        $email = $request->user()->email;
        $vendorContact = VendorContact::where('email', $email)->first();

        if (!$vendorContact) {
            return response()->json(['success' => false, 'message' => 'No vendor associated with this email'], 404);
        }

        $addresses = VendorAddress::where('vendor_id', $vendorContact->vendor_id)->get();

        return response()->json([
            'success' => true,
            'data' => $addresses
        ]);
        }
    }

    // Store a new address (only for the authenticated user's vendor)
    public function store(Request $request)
    {
        $user = $request->user();
        if ($user->hasRole('admin')) {
            // Admin: allow access to all records, skip vendor email check
            return response()->json([
            'message' => 'ADMIN CHECK',
        ], 201);
        } else {
        $email = user()->email;
        $vendorContact = VendorContact::where('email', $email)->first();

        if (!$vendorContact || $vendorContact->vendor_id != $request->vendor_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'address_type' => 'required|in:BILLING,SHIPPING,REGISTERED,OTHER',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state_province' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'po_box' => 'nullable|string|max:20',
            'country' => 'required|string|max:100',
            'country_code' => 'required|string|max:5'
        ]);

        $address = VendorAddress::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Vendor address created',
            'data' => $address
        ], 201);
        }
    }

    // Show a single address (only if the user is associated with the vendor)
    public function show(Request $request, VendorAddress $vendor_address)
    {
        $user = $request->user();
        if ($user->hasRole('admin')) {
            // Admin: allow access to all records, skip vendor email check
            return response()->json([
            'message' => 'ADMIN CHECK',
        ], 201);
        } else {
        $email = user()->email;
        $vendorContact = VendorContact::where('email', $email)
            ->where('vendor_id', $vendor_address->vendor_id)
            ->first();

        if (!$vendorContact) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $vendor_address
        ]);
        }
    }

    // Update an address (only if the user is associated with the vendor)
    public function update(Request $request, VendorAddress $vendor_address)
    {
        $user = $request->user();
        if ($user->hasRole('admin')) {
            // Admin: allow access to all records, skip vendor email check
            return response()->json([
            'message' => 'ADMIN CHECK',
        ], 201);
        } else {
        $email = user()->email;
        $vendorContact = VendorContact::where('email', $email)
            ->where('vendor_id', $vendor_address->vendor_id)
            ->first();

        if (!$vendorContact) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'address_type' => 'in:BILLING,SHIPPING,REGISTERED,OTHER',
            'address_line1' => 'string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'string|max:100',
            'state_province' => 'string|max:100',
            'postal_code' => 'string|max:20',
            'po_box' => 'nullable|string|max:20',
            'country' => 'string|max:100',
            'country_code' => 'string|max:5'
        ]);

        $vendor_address->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Vendor address updated',
            'data' => $vendor_address
        ]);
        }
    }

    // Delete an address (only if the user is associated with the vendor)
    public function destroy(Request $request, VendorAddress $vendor_address)
    {
        $user = $request->user();
        if ($user->hasRole('admin')) {
            // Admin: allow access to all records, skip vendor email check
            return response()->json([
            'message' => 'ADMIN CHECK',
        ], 201);
        } else {
        $email = user()->email;
        $vendorContact = VendorContact::where('email', $email)
            ->where('vendor_id', $vendor_address->vendor_id)
            ->first();

        if (!$vendorContact) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $vendor_address->delete();

        return response()->json([
            'success' => true,
            'message' => 'Vendor address deleted'
        ]);
    }
    }
}

