<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vendor;
use App\Models\VendorBusinessDetail;
use App\Models\VendorAddress;
use App\Models\VendorContact;
use App\Models\VendorPurchasingOrg;
use App\Models\VendorBank;

class VendorProfileController extends Controller
{
    /**
     * Display vendor profile with all details
     */
    public function showProfile($vendorId)
    {
        $vendor = Vendor::with([
            'bankDetails',
            'contacts',
            'addresses',
            'businessDetails',
            'purchasingOrgs',
        ])->findOrFail($vendorId);
    
        return view('profile', compact('vendor'));
    }

    /**
     * Save vendor profile updates
     */
    public function save(Request $request, Vendor $vendor)
{
    $request->validate([
        'vendor_name' => 'required|string|max:255',
        'vendor_code' => 'nullable|string|max:100',
    ]);

    // ✅ Use the $vendor passed via route model binding
    if (!$vendor) {
        return redirect()->back()->with('error', 'Vendor not found.');
    }

    // update vendor core
    $vendor->vendor_name = $request->vendor_name;
    $vendor->vendor_code = $request->vendor_code;
    $vendor->save();

    // business details
    $business = $vendor->businessDetails ?? new VendorBusinessDetail(['vendor_id' => $vendor->id]);
    $business->supplier_status = $request->status;
    $business->save();

    // contact
    $contact = $vendor->contacts->first() ?? new VendorContact([
        'vendor_id' => $vendor->id,
        'contact_id' => uniqid('contact_')
    ]);
    $contact->email = $request->email;
    $contact->phone = $request->phone;
    $contact->mobile = $request->mobile;
    $contact->save();

    // address
    $address = $vendor->addresses->first() ?? new VendorAddress(['vendor_id' => $vendor->id]);
    $address->address_line1 = $request->address_line1;
    $address->address_line2 = $request->address_line2;
    $address->city = $request->city;
    $address->country = $request->country_address;
    $address->postal_code = $request->postal_code;
    $address->po_box = $request->po_box;
    $address->save();

    // purchasing org
    $purchasingOrg = $vendor->purchasingOrgs->first() ?? new VendorPurchasingOrg(['vendor_id' => $vendor->id]);
    $purchasingOrg->purchasing_org = $request->purchase_org;
    $purchasingOrg->save();

    return redirect()->route('vendor.profile', ['vendor_id' => $vendor->id])->with('success', 'Vendor profile updated successfully!');
    }

}