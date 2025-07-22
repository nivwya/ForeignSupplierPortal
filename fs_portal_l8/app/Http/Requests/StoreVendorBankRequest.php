<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVendorBankRequest extends FormRequest
{
    public function authorize()
    {
        // Only allow logged-in users (basic check)
        //return auth()->check();
        return true; //for now LATER CHANGEE
    }

    public function rules()
    {
        return [
            'vendor_id' => 'required|exists:vendors,id',
            'bank_country' => 'required|string|size:2',
            'bank_key' => 'required|string|max:50',
            'bank_account' => 'required|string|max:50',
            'bank_control_key' => 'required|string|max:10',
            'partner_bank_type' => 'required|in:PRIMARY,SECONDARY',
            'collection_authorization' => 'required|string|max:20',
            'reference_details' => 'required|string|max:100',
            'account_holder' => 'required|string|max:255',
            'account_description' => 'required|string|max:255',
            'status_bk_details_hd' => 'required|string|max:50',
            'valid_from' => 'required|date',
            'eff_to' => 'required|date|after_or_equal:valid_from',
            'is_active' => 'required|boolean'
        ];
    }
}
