<?php

namespace App\Http\Controllers;

use App\Models\VendorPurchasingOrg;
use App\Models\VendorContact;
use Illuminate\Http\Request;

class VendorPurchasingOrgController extends Controller
{
    // List all purchasing orgs for the authenticated user's vendor(s)
    public function index(Request $request)
    {
        $email = $request->user()->email;
        $vendorContact = VendorContact::where('email', $email)->first();

        if (!$vendorContact) {
            return response()->json(['success' => false, 'message' => 'No vendor associated with this email'], 404);
        }

        $purchasingOrgs = VendorPurchasingOrg::where('vendor_id', $vendorContact->vendor_id)->get();

        return response()->json([
            'success' => true,
            'data' => $purchasingOrgs
        ]);
    }

    // Store a new purchasing org (only for the authenticated user's vendor)
    public function store(Request $request)
    {
        $email = $request->user()->email;
        $vendorContact = VendorContact::where('email', $email)->first();

        if (!$vendorContact || $vendorContact->vendor_id != $request->vendor_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'purchasing_org' => 'required|string|max:20',
            'order_currency' => 'required|string|max:10',
            'min_order_value' => 'required|numeric',
            'terms_of_payment' => 'required|string|max:50',
            'incoterms' => 'required|string|max:50'
        ]);

        $purchasingOrg = VendorPurchasingOrg::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Purchasing org created',
            'data' => $purchasingOrg
        ], 201);
    }

    // Show a single purchasing org (only if the user is associated with the vendor)
    public function show(Request $request, VendorPurchasingOrg $vendor_purchasing_org)
    {
        $email = $request->user()->email;
        $vendorContact = VendorContact::where('email', $email)
            ->where('vendor_id', $vendor_purchasing_org->vendor_id)
            ->first();

        if (!$vendorContact) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $vendor_purchasing_org
        ]);
    }

    // Update a purchasing org (only if the user is associated with the vendor)
    public function update(Request $request, VendorPurchasingOrg $vendor_purchasing_org)
    {
        $email = $request->user()->email;
        $vendorContact = VendorContact::where('email', $email)
            ->where('vendor_id', $vendor_purchasing_org->vendor_id)
            ->first();

        if (!$vendorContact) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'purchasing_org' => 'string|max:20',
            'order_currency' => 'string|max:10',
            'min_order_value' => 'numeric',
            'terms_of_payment' => 'string|max:50',
            'incoterms' => 'string|max:50'
        ]);

        $vendor_purchasing_org->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Purchasing org updated',
            'data' => $vendor_purchasing_org
        ]);
    }

    // Delete a purchasing org (only if the user is associated with the vendor)
    public function destroy(Request $request, VendorPurchasingOrg $vendor_purchasing_org)
    {
        $email = $request->user()->email;
        $vendorContact = VendorContact::where('email', $email)
            ->where('vendor_id', $vendor_purchasing_org->vendor_id)
            ->first();

        if (!$vendorContact) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $vendor_purchasing_org->delete();

        return response()->json([
            'success' => true,
            'message' => 'Purchasing org deleted'
        ]);
    }
}
