<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VendorCompanyCode extends Model
{
    use HasFactory;

    // Explicitly set the table name
    protected $table = 'vendor_company_codes';

    // Fillable fields based on your ERD
    protected $fillable = [
        'vendor_id',
        'company_code',
        'account_number',
        'reconciliation_account',
        'payment_term',
        'payment_block',
        'head_office_account_number', // matches your ERD naming
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships

    /**
     * Each company code record belongs to one vendor
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id', 'id');

    }
}

