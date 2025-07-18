<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    /**
     * Display a listing of vendors specific to user id , got trhough vendor_contacts email
     */
    public function index(Request $request)
    {
        $email = $request->user()->email;
        $vendorContact = \App\Models\VendorContact::where('email', $email)->first();

        if (!$vendorContact) {
            return response()->json(['success' => false, 'message' => 'No vendor associated with this email'], 404);
        }

        $vendor = \App\Models\Vendor::with([
            'bankDetails',
            'contacts',
            'addresses',
            'businessDetails',
            'companyCodes'
        ])->where('id', $vendorContact->vendor_id)->first();

        return response()->json([
            'success' => true,
            'data' => $vendor
        ]);
    }


    /**
     * Store a newly created vendor
     */
    public function store(Request $request)
    {
        // Basic validation
        $request->validate([
            'vendor_code' => 'required|unique:vendors',
            'vendor_name' => 'required|string|max:255',
        ]);

        // Create the vendor
        $vendor = Vendor::create([
            'vendor_code' => $request->vendor_code,
            'vendor_name' => $request->vendor_name,
            'authorization_group' => $request->authorization_group,
            'account_group' => $request->account_group,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Vendor created successfully',
            'data' => $vendor
        ], 201);
    }

    /**
     * Display a specific vendor mapped with a user
     */
    public function show(Request $request, $id)
    {
        $email = $request->user()->email;
        $vendorContact = \App\Models\VendorContact::where('email', $email)
            ->where('vendor_id', $id)
            ->first();

        if (!$vendorContact) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $vendor = \App\Models\Vendor::with([
            'bankDetails',
            'contacts',
            'addresses',
            'businessDetails',
            'companyCodes',
            'purchasingOrgs',
            'auditLogs'
        ])->findOrFail($id);

        return response()->json(['success' => true, 'data' => $vendor]);
    }
    /**
     * Update a vendor
     */
    public function update(Request $request, Vendor $vendor)
    {
        $email = $request->user()->email;
        $vendorContact = \App\Models\VendorContact::where('email', $email)
            ->where('vendor_id', $vendor->id)
            ->first();

        if (!$vendorContact) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'vendor_code' => 'required|unique:vendors,vendor_code,' . $vendor->id,
            'vendor_name' => 'required|string|max:255',
        ]);

        $vendor->update([
            'vendor_code' => $request->vendor_code,
            'vendor_name' => $request->vendor_name,
            'authorization_group' => $request->authorization_group,
            'account_group' => $request->account_group,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Vendor updated successfully',
            'data' => $vendor
        ]);
    }


    /**
     * Delete a vendor
     */
   public function destroy(Request $request, Vendor $vendor)
    {
        $email = $request->user()->email;
        $vendorContact = \App\Models\VendorContact::where('email', $email)
            ->where('vendor_id', $vendor->id)
            ->first();

        if (!$vendorContact) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $vendor->delete();

        return response()->json([
            'success' => true,
            'message' => 'Vendor deleted successfully'
        ]);
    }
}
