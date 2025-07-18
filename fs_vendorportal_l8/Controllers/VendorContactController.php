<?php

namespace App\Http\Controllers;

use App\Models\VendorContact;
use Illuminate\Http\Request;

class VendorContactController extends Controller
{
    // List all contacts for the authenticated user's vendor(s)
    public function index(Request $request)
    {
        $email = $request->user()->email;
        $vendorContact = VendorContact::where('email', $email)->first();

        if (!$vendorContact) {
            return response()->json(['success' => false, 'message' => 'No vendor associated with this email'], 404);
        }

        // Only return contacts for the vendor(s) associated with this email
        $contacts = VendorContact::where('vendor_id', $vendorContact->vendor_id)->get();

        return response()->json([
            'success' => true,
            'data' => $contacts
        ]);
    }

    // Store a new contact (only for the authenticated user's vendor)
   public function store(Request $request)
    {
        $email = $request->user()->email;
        $vendorId = $request->vendor_id;

        // Check if any contacts exist for this vendor
        $existingContact = \App\Models\VendorContact::where('vendor_id', $vendorId)->exists();

        if ($existingContact) {
            // Enforce normal access control: user must already be a contact for this vendor
            $vendorContact = \App\Models\VendorContact::where('email', $email)
                ->where('vendor_id', $vendorId)
                ->first();
            if (!$vendorContact) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
        } else {
            // No contacts exist yet: only allow if the contact's email matches the user's email
            if ($request->email !== $email) {
                return response()->json(['success' => false, 'message' => 'First contact must use your email'], 403);
            }
        }

        $validated = $request->validate([
            'contact_id' => 'required|string|unique:vendor_contacts,contact_id',
            'vendor_id' => 'required|exists:vendors,id',
            'contact_type' => 'required|in:PRIMARY,BILLING,SHIPPING,TECHNICAL',
            'contact_person' => 'required|string|max:100',
            'department' => 'required|string',
            'phone' => 'required|string',
            'fax' => 'nullable|string',
            'email' => 'required|email',
            'mobile' => 'nullable|string',
        ]);

        $contact = \App\Models\VendorContact::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Contact created successfully',
            'data' => $contact
        ], 201);
    }

    // Show a single contact (only if the user is associated with the vendor)
    public function show(Request $request, $id)
    {
        $contact = VendorContact::findOrFail($id);
        $email = $request->user()->email;
        $vendorContact = VendorContact::where('email', $email)
            ->where('vendor_id', $contact->vendor_id)
            ->first();

        if (!$vendorContact) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $contact
        ]);
    }

    // Update a contact (only if the user is associated with the vendor)
    public function update(Request $request, $id)
    {
        $contact = VendorContact::findOrFail($id);
        $email = $request->user()->email;
        $vendorContact = VendorContact::where('email', $email)
            ->where('vendor_id', $contact->vendor_id)
            ->first();

        if (!$vendorContact) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'contact_type' => 'in:PRIMARY,BILLING,SHIPPING,TECHNICAL',
            'contact_person' => 'string|max:100',
            'department' => 'string',
            'phone' => 'string',
            'fax' => 'nullable|string',
            'email' => 'email',
            'mobile' => 'nullable|string',
        ]);

        $contact->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Contact updated successfully',
            'data' => $contact
        ]);
    }

    // Delete a contact (only if the user is associated with the vendor)
    public function destroy(Request $request, $id)
    {
        $contact = VendorContact::findOrFail($id);
        $email = $request->user()->email;
        $vendorContact = VendorContact::where('email', $email)
            ->where('vendor_id', $contact->vendor_id)
            ->first();

        if (!$vendorContact) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $contact->delete();

        return response()->json([
            'success' => true,
            'message' => 'Contact deleted successfully'
        ]);
    }
}


