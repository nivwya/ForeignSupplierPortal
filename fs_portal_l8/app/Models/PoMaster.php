<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PoMaster extends Model
{
    protected $table = 'v002_pomaster';
    public $timestamps = false;
    protected $primaryKey = 'purchase_doc_no';
    public $incrementing = false;

    protected $fillable = [
        'purchase_doc_no', 'item_number_doc', 'vendor_account_no', 'payment_key',
        'TEXT1', 'del_indicator', 'purchase_order_dt', 'company_code',
        'company_name', 'purchase_org', 'desc_purchase_org', 'purchase_group',
        'desc_purchase_grp', 'plant', 'PLANT_NAME1', 'storage_loc', 'desc_storage_loc',
        'rec_created_dt', 'sale_response', 'TELF1', 'rels_indicator', 'short_text',
        'material_number', 'net_price', 'price_unit', 'net_order', 'gross_order',
        'purchase_order_qty', 'unit_of_mesaure', 'currency_key',
        'ack_status', 'po_pdf', 'ADD_TEXT3', 'ADD_TEXT4', 'ADD_TEXT5',
        'CREATED_ON', 'CREATED_AT', 'CREATED_BY'
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_account_no', 'LIFNR');
    }

    public function deliveries()
    {
        return $this->hasMany(PoDelivery::class, 'prchase_doc_number', 'purchase_doc_no');
    }

    public function invoices()
    {
        return $this->hasMany(InvoiceData::class, 'purchase_doc_no', 'purchase_doc_no');
    }

    public function grnDeliveries()
    {
        return $this->hasMany(GrnDelivery::class, 'po_number', 'purchase_doc_no');
    }

    public function deliveriesRelation()
    {
        return $this->hasMany(\App\Models\Delivery::class, 'order_id', 'purchase_doc_no');
    }
    public function items()
    {
        return $this->hasMany(self::class, 'purchase_doc_no', 'purchase_doc_no');
    }
}
