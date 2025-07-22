<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VendorAddress extends Model
{
    use HasFactory;

    // Explicitly set the table name
    protected $table = 'vendor_address';

    // Set primary key if different from 'id'
    protected $primaryKey = 'id';

    // Fillable fields based on your ERD
    protected $fillable = [
        'vendor_id',
        'address_type',
        'address_line1',
        'address_line2',
        'city',
        'state_province',
        'postal_code',
        'po_box',
        'country',
        'country_code',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'address_type' => 'string', // enum in database
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // RELATIONSHIPS

    /**
     * Each address belongs to one vendor
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id', 'id');
    }

    // SCOPES (for common queries)

    /**
     * Get addresses by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('address_type', $type);
    }

    /**
     * Get addresses by country
     */
    public function scopeByCountry($query, $country)
    {
        return $query->where('country', $country);
    }

    // ACCESSORS (modify data when retrieving)

    /**
     * Get full address as a single string
     */
    public function getFullAddressAttribute()
    {
        $parts = array_filter([
            $this->address_line1,
            $this->address_line2,
            $this->city,
            $this->state_province,
            $this->postal_code,
            $this->country
        ]);

        return implode(', ', $parts);
    }

    /**
     * Get formatted address for display
     */
    public function getFormattedAddressAttribute()
    {
        $formatted = $this->address_line1;
        
        if ($this->address_line2) {
            $formatted .= "\n" . $this->address_line2;
        }
        
        $formatted .= "\n" . $this->city;
        
        if ($this->state_province) {
            $formatted .= ", " . $this->state_province;
        }
        
        if ($this->postal_code) {
            $formatted .= " " . $this->postal_code;
        }
        
        $formatted .= "\n" . $this->country;
        
        return $formatted;
    }

    // MUTATORS (modify data when saving)

    /**
     * Store country code in uppercase
     */
    public function setCountryCodeAttribute($value)
    {
        $this->attributes['country_code'] = strtoupper($value);
    }

    /**
     * Store address type in uppercase
     */
    public function setAddressTypeAttribute($value)
    {
        $this->attributes['address_type'] = strtoupper($value);
    }

    // CUSTOM METHODS

    /**
     * Check if this is a primary address
     */
    public function isPrimary()
    {
        return strtoupper($this->address_type) === 'PRIMARY';
    }

    /**
     * Check if this is a billing address
     */
    public function isBilling()
    {
        return strtoupper($this->address_type) === 'BILLING';
    }

    /**
     * Check if this is a shipping address
     */
    public function isShipping()
    {
        return strtoupper($this->address_type) === 'SHIPPING';
    }

    /**
     * Mark this address as primary for the vendor
     */
    public function markAsPrimary()
    {
        // First, remove primary status from other addresses
        self::where('vendor_id', $this->vendor_id)
            ->where('address_id', '!=', $this->address_id)
            ->where('address_type', 'PRIMARY')
            ->update(['address_type' => 'SECONDARY']);

        // Set this as primary
        $this->update(['address_type' => 'PRIMARY']);
    }
}

