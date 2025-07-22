<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vendor;

class VendorProfileController extends Controller
{
    public function showProfile($vendor_id)
{
    $vendor = Vendor::where('LIFNR', $vendor_id)->firstOrFail();
    return view('profile', compact('vendor'));
}


    public function save(Request $request, Vendor $vendor)
    {
        $request->validate([
            'vendor_name' => 'required|string|max:255',
            'status' => 'nullable|string', // optional if you keep it in this table
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'mobile' => 'nullable|string|max:50',
            'house_number' => 'nullable|string|max:100',
            'street_2' => 'nullable|string|max:255',
            'building' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'country_address' => 'nullable|string|max:255',
            'po_box' => 'nullable|string|max:50',
            'postal_code' => 'nullable|string|max:50',
            'purchase_org' => 'nullable|string|max:100',
        ]);

        // Update vendor fields in v001_vendor
        $vendor->NAME1 = $request->vendor_name;
        // LIFNR is the vendor ID (primary key) - don't update this unless you want to allow vendor ID change
        // $vendor->LIFNR = $request->vendor_code; // usually primary key, so avoid changing here unless intentional

        $vendor->EMAIL = $request->email;
        $vendor->TELF1 = $request->phone;
        $vendor->MOBILE = $request->mobile ?? null; // add MOBILE column if exists

        $vendor->HOUSE_NUM1 = $request->house_number;
        $vendor->STR_SUPPL1 = $request->street_2;
        $vendor->BUILDING = $request->building;
        $vendor->CITY1 = $request->city;
        $vendor->LAND1 = $request->country_address;
        $vendor->PFACH = $request->po_box;
        $vendor->PSTL2 = $request->postal_code;

        $vendor->PURCH_ORG = $request->purchase_org ?? null; // if this column exists in v001_vendor

        // Supplier status might be stored in this table, otherwise remove this
        $vendor->SUPPLIER_STATUS = $request->status ?? null;

        $vendor->save();

        return redirect()->route('vendor.profile', ['vendor_id' => $vendor->LIFNR])
                         ->with('success', 'Vendor profile updated successfully!');
    }
}
