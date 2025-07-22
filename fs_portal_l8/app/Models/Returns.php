<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Returns extends Model
{
    use HasFactory;

    protected $table = 'returns';

    protected $fillable = [
        'po_number',
        'material_code',
        'return_date',
        'quantity_returned',
        'reason',
        'credit_note_issued',
        'credit_note_amount',
        'follow_up_status',
    ];
}
