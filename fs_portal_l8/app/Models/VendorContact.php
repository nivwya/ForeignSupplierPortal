<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VendorContact extends Model
{
    use HasFactory;

    // Table name as per your ERD
    protected $table = 'vendor_contacts';

    // Primary key is contact_id (varchar), not id
    protected $primaryKey = 'contact_id';
    public $incrementing = false; // Since contact_id is varchar, not auto-increment
    protected $keyType = 'string';

    // Fillable fields from your ERD
    protected $fillable = [
        'contact_id',
        'vendor_id',
        'contact_type',
        'contact_person',
        'department',
        'phone',
        'fax',
        'email',
        'mobile',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'created_at' => 'date',
        'updated_at' => 'date',
    ];

    /**
     * Each contact belongs to one vendor
     */
   public function vendor()
        {
            return $this->belongsTo(Vendor::class, 'vendor_id', 'id');
        }

}
