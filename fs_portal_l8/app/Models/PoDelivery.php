<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PoDelivery extends Model
{
    protected $table = 'v003_podelivery';
    public $timestamps = false;

    protected $fillable = [
        'prchase_doc_number', 'item_no', 'del_schedule_no', 'itm_delivery_dt',
        'stat_del_dt', 'schedule_qty', 'replenish_qty', 'goods_qty', 'issued_qty',
        'timing', 'order_dt', 'batch_no', 'unit_measure',
        'ADD_TEXT1', 'ADD_TEXT2', 'ADD_TEXT3', 'ADD_TEXT4', 'ADD_TEXT5'
    ];

    public function poMaster()
    {
        return $this->belongsTo(PoMaster::class, 'prchase_doc_number', 'purchase_doc_no');
    }
}
