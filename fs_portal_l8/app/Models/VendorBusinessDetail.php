<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VendorBusinessDetail extends Model
{
    use HasFactory;

    // Table name as per migration and ERD
    protected $table = 'vendor_business_details';

    // Fillable fields from migration and ERD
    protected $fillable = [
        'vendor_id',
        'supplier_type',
        'supplier_status',
        'supplier_classification',
        'supplier_category',
        'payment_terms',
        'currency',
        'tax_number',
        'vat_number',
        'registration_number',
        'license_number',
        'license_expiry',
        'website',
        'remarks',
    ];

    protected $casts = [
        'license_expiry' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Each business detail record belongs to one vendor
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id', 'id');
    }
}

