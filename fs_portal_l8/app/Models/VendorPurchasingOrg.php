<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VendorPurchasingOrg extends Model
{
    use HasFactory;

    // Table name as per migration and ERD
    protected $table = 'vendor_purchasing_org';

    // Fillable fields based on your migration and ERD
    protected $fillable = [
        'vendor_id',
        'purchasing_org',
        'order_currency',
        'min_order_value',
        'terms_of_payment',
        'incoterms',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'min_order_value' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Each purchasing org record belongs to one vendor
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id', 'id');
    }
}
