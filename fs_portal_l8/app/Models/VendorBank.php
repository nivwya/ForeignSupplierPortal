<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VendorBank extends Model
{
    use HasFactory;

    // Table name (if different from plural of model name)
    protected $table = 'vendor_bank';

    // Fields that can be mass-assigned (for security)
    protected $fillable = [
        'vendor_id',
        'bank_country',
        'bank_key',
        'bank_account',
        'bank_control_key',
        'partner_bank_type',
        'collection_authorization',
        'reference_details',
        'account_holder',
        'account_description',
        'status_bk_details_hd',
        'valid_from',
        'eff_to',
        'is_active',
    ];

    // Fields that should be cast to specific types
    protected $casts = [
        'valid_from' => 'date',
        'eff_to' => 'date',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // RELATIONSHIPS

    /**
     * Each bank record belongs to one vendor
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    // SCOPES (for common queries)

    /**
     * Get only active bank accounts
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get bank accounts by country
     */
    public function scopeByCountry($query, $country)
    {
        return $query->where('bank_country', $country);
    }

    /**
     * Get valid bank accounts (within date range)
     */
    public function scopeValid($query)
    {
        $today = now()->toDateString();
        return $query->where('valid_from', '<=', $today)
                     ->where('eff_to', '>=', $today);
    }

    // ACCESSORS (modify data when retrieving)

    /**
     * Format bank account number (hide middle digits for security)
     */
    public function getMaskedBankAccountAttribute()
    {
        $account = $this->bank_account;
        if (strlen($account) > 8) {
            return substr($account, 0, 4) . '****' . substr($account, -4);
        }
        return $account;
    }

    /**
     * Get full bank description
     */
    public function getFullDescriptionAttribute()
    {
        return $this->account_holder . ' - ' . $this->account_description;
    }

    // MUTATORS (modify data when saving)

    /**
     * Store bank account in uppercase
     */
    public function setBankAccountAttribute($value)
    {
        $this->attributes['bank_account'] = strtoupper($value);
    }

    /**
     * Store bank country in uppercase
     */
    public function setBankCountryAttribute($value)
    {
        $this->attributes['bank_country'] = strtoupper($value);
    }

    // CUSTOM METHODS

    /**
     * Check if bank account is currently valid
     */
    public function isCurrentlyValid()
    {
        $today = now()->toDateString();
        return $this->valid_from <= $today && $this->eff_to >= $today;
    }

    /**
     * Get bank account status
     */
    public function getStatus()
    {
        if (!$this->is_active) {
            return 'Inactive';
        }

        if (!$this->isCurrentlyValid()) {
            return 'Expired';
        }

        return 'Active';
    }

    /**
     * Mark bank account as primary for vendor
     */
    public function markAsPrimary()
    {
        // First, remove primary status from other accounts
        self::where('vendor_id', $this->vendor_id)
            ->where('id', '!=', $this->id)
            ->update(['partner_bank_type' => 'SECONDARY']);

        // Set this as primary
        $this->update(['partner_bank_type' => 'PRIMARY']);
    }
}

