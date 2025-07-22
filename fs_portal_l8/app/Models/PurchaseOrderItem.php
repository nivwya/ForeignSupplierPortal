<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseOrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_code',
        'line_item_no',
        'item_description',
        'quantity',
        'uom',
        'price',
        'value',
        'plant',
        'slocc',
        'status',
        'delivery_date',
        'material_group',
        'delivered_quantity',
        'invoiced_quantity',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'order_id');
    }
}
