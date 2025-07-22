<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceData extends Model
{
    protected $table = 'v005_invoice_data';
    public $timestamps = false;

    protected $fillable = [
        'account_doc_no', 'fiscal_year', 'invoice_itm_doc', 'document_dt',
        'post_doc_date', 'baseline_due_dt', 'purchase_doc_no', 'item_no_doc',
        'sequence_no', 'material_num', 'company_code', 'plant', 'Currency_Key',
        'amount', 'dc_indicator', 'Quantity', 'po_UOM', 'po_qty', 'priceunit',
        'total_stock_value', 'prev_stock_value', 'base_unit', 'reference_docs',
        'fiscal_year2', 'item_ref_doc',
        'ADD_TEXT1', 'ADD_TEXT2', 'ADD_TEXT3', 'ADD_TEXT4', 'ADD_TEXT5',
        'CREATED_ON', 'CREATED_AT', 'CREATED_BY'
    ];

    public function poMaster()
    {
        return $this->belongsTo(PoMaster::class, 'purchase_doc_no', 'purchase_doc_no');
    }
}
