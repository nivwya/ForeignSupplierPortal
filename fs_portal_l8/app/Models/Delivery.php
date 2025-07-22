<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Delivery extends Model
{
    protected $fillable = [
        'order_id',
        'delivery_date',
        'delivery_number',
        'company',
        'department',
        'order_value',
        'currency',
        'status',
        'grn_num',
        'grn_pdf',
        'grn_date',
        'reconciliation_account',
        'authorization_group',
        'payment_block',
        'head_office_account_number',
        'notes',
        'confirmed_by',
        'confirmed_at'

    ];

    // Relationships

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'order_id');
    }

    public function items()
    {
        return $this->hasMany(DeliveryItem::class, 'delivery_id');
    }
}
