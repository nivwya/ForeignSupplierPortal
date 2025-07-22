<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number',
        'purchase_order_id',
        'delivery_id',
        'amount',
        'amount_paid',
        'amount_due',
        'status',
        'invoice_date',
        'due_date',
        'invoice_pdf',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'amount_due' => 'decimal:2',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }

    public function delivery()
    {
        return $this->belongsTo(Delivery::class, 'delivery_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
