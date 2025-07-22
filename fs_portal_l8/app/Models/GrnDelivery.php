<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GrnDelivery extends Model
{
    protected $table = 'v004_grn_delivery';
    public $timestamps = false;
    protected $primaryKey = ['purchase_doc_no', 'item_number', 'sequential_number'];
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'purchase_doc_no',
        'item_number',
        'sequential_number',
        'transaction_type',
        'material_doc_year',
        'no_of_material_doc',
        'item_material_doc',
        'po_history',
        'movement_type',
        'posting_date',
        'quantity',
        'qty_priceunit',
        'amt_in_local',
        'amt_in_doc',
        'currency_key',
        'gr_ir_value',
        'debit_credit_indicator',
        'reference_doc_no',
        'fiscal_year',
        'doc_no_reference',
        'item_ref_doc',
        'movement_reason',
        'account_created_dt',
        'time_of_entry',
        'invoice_value_local',
        'shipping_complaince',
        'invoice_value_foriegn',
        'material_number',
        'plant',
        'gr_ir_clearing_acc',
        'local_currency_key',
        'Qauntity',
        'gr_ir_acc_po_currency',
        'invoice_amt_po_currency',
        'ADD_TEXT1',
        'ADD_TEXT2',
        'ADD_TEXT3',
        'ADD_TEXT4',
        'ADD_TEXT5',
        'created_on',
        'created_at',
        'created_by',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_acc_no', 'LIFNR');
    }

    public function poMaster()
    {
        return $this->belongsTo(PoMaster::class, 'po_number', 'purchase_doc_no');
    }
}
