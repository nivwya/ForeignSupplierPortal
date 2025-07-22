<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseOrder extends Model
{
    // If your table name is not the default plural, specify it:
    // protected $table = 'purchase_orders';

    protected $fillable = [
        'order_number',
        'vendor_id',
        'amg_company_code',
        'order_date',
        'delivery_date',
        'company',
        'department',
        'order_value',
        'currency',
        'payment_term',
        'status',
        'acknowledgement_date',
        'po_pdf',
        'created_at',
        'updated_at',
    ];

    // Relationships

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class, 'order_id');
    }

    public function deliveries()
    {
        return $this->hasMany(Delivery::class, 'order_id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'order_id');
    }
}
