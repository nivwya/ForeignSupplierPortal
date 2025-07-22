<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DeliveryItem extends Model
{
    protected $fillable = [
        'delivery_id',
        'purchase_order_item_id',
        'line_item_num',
        'item_description',
        'quantity',
        'uom',
        'expected_delv_date',
        'quantity_supplied',
        'supply_date',
        'delivery_note',
        'qty_received_by_amg',
        'amg_received_date',
        'batch_number',
        'serial_number',
        'unit_price',
        'total_value',
        'status',
        'grn_pdf',
        'remarks',
        'storage_location',
        'plant',
    ];

    // Relationships

    public function delivery()
    {
        return $this->belongsTo(Delivery::class, 'delivery_id');
    }

    public function purchaseOrderItem()
    {
        return $this->belongsTo(PurchaseOrderItem::class, 'purchase_order_item_id');
    }
}
