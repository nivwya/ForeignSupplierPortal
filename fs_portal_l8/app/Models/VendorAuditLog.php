<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VendorAuditLog extends Model
{
    use HasFactory;

    // Table name as per migration and ERD
    protected $table = 'vendor_audit_log';

    // Fillable fields based on your migration
    protected $fillable = [
        'vendor_id',
        'table_name',
        'action_type',
        'field_name',
        'old_values',
        'new_values',
        'changed_by',
        'change_timestamp',
        'company_code',
        'change_reason',
        'approval_status',
        'approved_by',
        'approved_at',
        'ip_address',
        'user_agent',
        'comments',
    ];

    protected $casts = [
        'old_values' => 'json',
        'new_values' => 'json',
        'change_timestamp' => 'datetime',
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // RELATIONSHIPS

    /**
     * Each audit log entry belongs to one vendor
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id', 'id');
    }

    // SCOPES (for common queries)

    /**
     * Get audit logs by action type
     */
    public function scopeByAction($query, $action)
    {
        return $query->where('action_type', $action);
    }

    /**
     * Get audit logs by table name
     */
    public function scopeByTable($query, $tableName)
    {
        return $query->where('table_name', $tableName);
    }

    /**
     * Get audit logs by user
     */
    public function scopeByUser($query, $user)
    {
        return $query->where('changed_by', $user);
    }

    /**
     * Get pending approval logs
     */
    public function scopePendingApproval($query)
    {
        return $query->where('approval_status', 'PENDING');
    }

    /**
     * Get approved logs
     */
    public function scopeApproved($query)
    {
        return $query->where('approval_status', 'APPROVED');
    }

    // ACCESSORS

    /**
     * Get formatted change summary
     */
    public function getChangeSummaryAttribute()
    {
        return "{$this->action_type} on {$this->table_name} by {$this->changed_by}";
    }

    // CUSTOM METHODS

    /**
     * Check if the log entry is pending approval
     */
    public function isPending()
    {
        return $this->approval_status === 'PENDING';
    }

    /**
     * Check if the log entry is approved
     */
    public function isApproved()
    {
        return $this->approval_status === 'APPROVED';
    }

    /**
     * Check if the log entry is rejected
     */
    public function isRejected()
    {
        return $this->approval_status === 'REJECTED';
    }

    /**
     * Approve this audit log entry
     */
    public function approve($approvedBy)
    {
        $this->update([
            'approval_status' => 'APPROVED',
            'approved_by' => $approvedBy,
            'approved_at' => now(),
        ]);
    }

    /**
     * Reject this audit log entry
     */
    public function reject($approvedBy, $reason = null)
    {
        $this->update([
            'approval_status' => 'REJECTED',
            'approved_by' => $approvedBy,
            'approved_at' => now(),
            'comments' => $reason ? $this->comments . "\nRejection reason: " . $reason : $this->comments,
        ]);
    }
}
