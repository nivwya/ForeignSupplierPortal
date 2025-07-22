<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\PurchaseOrder;
use App\Models\Vendor;
use App\Models\Admin;
use App\Models\AdminCompanyCode;
class DashboardController extends Controller
{
     public function index()
    {
        $user = auth()->user();

        if ($user->isSuperAdmin()) {
            $purchaseOrders = PurchaseOrder::all();
            $vendors = Vendor::all();
        } else {
            // Regular admin: show only relevant company codes' POs
            $companyCodes = \App\Models\AdminCompanyCode::where('admin_email', $user->email)->pluck('company_code');

            $purchaseOrders = PurchaseOrder::whereIn('amg_company_code', $companyCodes)->get();
            $vendors = Vendor::all();
        }

        return view('admin.dashboard', [
            'purchaseOrders' => $purchaseOrders,
            'vendors' => $vendors,
        ]);
    }

    public function assignAdmin(Request $request)
    {
        $this->authorizeSuperAdmin();

        $validated = $request->validate([
            'email' => 'required|email',
            'company_code' => 'required|string|max:20'
        ]);

        // Explicit save
        $existing = AdminCompanyCode::where('admin_email', $validated['email'])
            ->where('company_code', $validated['company_code'])
            ->first();

        if (!$existing) {
            $adminCompanyCode = new AdminCompanyCode();
            $adminCompanyCode->admin_email = $validated['email'];
            $adminCompanyCode->company_code = $validated['company_code'];
            $adminCompanyCode->save();
        }

        return response()->json(['message' => 'Admin assigned successfully.']);
    }

    public function removeAdmin(Request $request)
    {
        $this->authorizeSuperAdmin();

        $validated = $request->validate([
            'email' => 'required|email',
            'company_code' => 'required|string|max:20'
        ]);

        AdminCompanyCode::where('admin_email', $validated['email'])
            ->where('company_code', $validated['company_code'])
            ->delete();

        return response()->json(['message' => 'Admin privileges revoked.']);
    }

    protected function authorizeSuperAdmin()
    {
        if (!auth()->user() || !auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized: Only SuperAdmin allowed');
        }
    }

//changes made by niveditha
    public function profile()
    {
        $adminCompanyArr = session('admin_company_profile');
        if ($adminCompanyArr) {
            $adminCompany = \App\Models\AdminCompanyCode::find($adminCompanyArr['id']);
        } else {
            $user = auth()->user();
            $adminCompany = \App\Models\AdminCompanyCode::where('admin_email', $user->email)->first();
        }
        return view('admin.admin_profile', ['admin' => $adminCompany]);
    }

    public function saveProfile(Request $request, $admin)
    {
        $adminCompany = \App\Models\AdminCompanyCode::findOrFail($admin);
        $adminCompany->company_code = $request->input('company_code');
        $adminCompany->emp_id = $request->input('emp_id');
        $adminCompany->po = $request->input('po');
        $adminCompany->mobile = $request->input('mobile');
        $adminCompany->status = $request->input('status');
        $adminCompany->role = $request->input('role');
        $adminCompany->save();
        // Update session for immediate UI consistency
        session(['admin_company_profile' => $adminCompany->toArray()]);
        return redirect()->route('admin.profile')->with('success', 'Admin profile updated successfully!');
    }

    public function changePassword(Request $request)
    {
        $data = $request->all();
        $oldPassword = $data['old_password'] ?? null;
        $newPassword = $data['new_password'] ?? null;

        // Get admin email from session (admin_company_profile)
        $adminCompanyArr = session('admin_company_profile');
        $adminEmail = $adminCompanyArr['admin_email'] ?? null;
        if (!$adminEmail) {
            return response()->json(['success' => false, 'message' => 'Admin email not found.'], 400);
        }

        $user = \App\Models\User::where('email', $adminEmail)->first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found for this admin email.'], 404);
        }

        if (!\Hash::check($oldPassword, $user->password)) {
            return response()->json(['success' => false, 'message' => 'Old password incorrect.'], 400);
        }

        $user->password = bcrypt($newPassword);
        $user->save();

        return response()->json(['success' => true, 'message' => 'Password updated successfully.']);
    }
}
//changes end