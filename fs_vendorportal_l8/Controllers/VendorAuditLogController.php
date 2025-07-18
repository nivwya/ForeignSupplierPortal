<?php

namespace App\Http\Controllers;

use App\Models\VendorAuditLog;
use Illuminate\Http\Request;

class VendorAuditLogController extends Controller
{
    // List all audit logs (with optional filters)
    public function index(Request $request)
    {
        $query = VendorAuditLog::query();

        // Optional filtering by vendor, action type, approval status, etc.
        if ($request->has('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }
        if ($request->has('action_type')) {
            $query->where('action_type', $request->action_type);
        }
        if ($request->has('approval_status')) {
            $query->where('approval_status', $request->approval_status);
        }

        return response()->json([
            'success' => true,
            'data' => $query->orderBy('change_timestamp', 'desc')->get()
        ]);
    }

    // Show a single audit log entry
    public function show(VendorAuditLog $vendor_audit_log)
    {
        return response()->json([
            'success' => true,
            'data' => $vendor_audit_log
        ]);
    }

    // Create a new audit log entry (usually, logs are created by the system, not users)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'table_name' => 'required|string|max:100',
            'action_type' => 'required|string|max:30',
            'field_name' => 'nullable|string|max:100',
            'old_values' => 'nullable|array',
            'new_values' => 'nullable|array',
            'changed_by' => 'required|string|max:100',
            'change_timestamp' => 'required|date',
            'company_code' => 'nullable|string|max:20',
            'change_reason' => 'nullable|string|max:255',
            'approval_status' => 'nullable|string|max:20',
            'approved_by' => 'nullable|string|max:100',
            'approved_at' => 'nullable|date',
            'ip_address' => 'nullable|ip',
            'user_agent' => 'nullable|string|max:255',
            'comments' => 'nullable|string|max:500'
        ]);

        $log = VendorAuditLog::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Audit log created',
            'data' => $log
        ], 201);
    }

    // Update an audit log (e.g., to approve/reject)
    public function update(Request $request, VendorAuditLog $vendor_audit_log)
    {
        $validated = $request->validate([
            'approval_status' => 'nullable|string|max:20',
            'approved_by' => 'nullable|string|max:100',
            'approved_at' => 'nullable|date',
            'comments' => 'nullable|string|max:500'
        ]);

        $vendor_audit_log->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Audit log updated',
            'data' => $vendor_audit_log
        ]);
    }

    // Delete an audit log (rarely used, but provided for completeness)
    public function destroy(VendorAuditLog $vendor_audit_log)
    {
        $vendor_audit_log->delete();

        return response()->json([
            'success' => true,
            'message' => 'Audit log deleted'
        ]);
    }
}
