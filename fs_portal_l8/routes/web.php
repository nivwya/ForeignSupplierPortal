<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthPageController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\VendorAddressController;
use App\Http\Controllers\VendorBankController;
use App\Http\Controllers\VendorBusinessDetailController;
use App\Http\Controllers\VendorCompanyCodeController;
use App\Http\Controllers\VendorContactController;
use App\Http\Controllers\VendorPurchasingOrgController;
use App\Http\Controllers\VendorAuditLogController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\VendorProfileController;



// Redirect root to auth
Route::redirect('/', '/auth');

// Unified auth routes
Route::get('/auth', [AuthPageController::class, 'show'])->name('auth');
Route::post('/auth', [AuthPageController::class, 'handle'])->name('auth.handle');

// Logout route
Route::post('/logout', function(Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/auth');
})->name('logout');

Route::get('/admin/dashboard', [DashboardController::class, 'index'])
    ->name('admin.dashboard')
    ->middleware(['auth', 'role:admin']);

// Dashboard route
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard')->middleware('auth');

// forgot password login page
Route::post('/password/send-otp', [ForgotPasswordController::class, 'sendOtp'])->name('password.sendOtp');
Route::post('/password/verify-otp', [ForgotPasswordController::class, 'verifyOtp'])->name('password.verifyOtp');
Route::post('/password/reset', [ForgotPasswordController::class, 'resetPassword'])->name('password.reset');

//vendor profile
use App\Models\Vendor;
Route::middleware('auth')->group(function () {
    // Vendor profile show route with vendor_id parameter
    Route::get('/vendor/profile/{vendor_id}', [VendorProfileController::class, 'showProfile'])
    ->name('vendor.profile')
    ->where('vendor_id', '.*'); // Allows IDs like 0000900010
    Route::post('/vendor/profile/save/{vendor}', [VendorProfileController::class, 'save'])->name('vendor.profile.save');
    Route::post('/vendor/change-password', function(Request $request) {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:8',
        ]);
        $user = auth()->user();
        if (!\Hash::check($request->old_password, $user->password)) {
            return response()->json(['success' => false, 'message' => 'Old password incorrect']);
        }
        $user->password = bcrypt($request->new_password);
        $user->save();
        return response()->json(['success' => true]);
    })->name('vendor.change.password');
});


// Home content route
Route::get('/dashboard/home-content', function () {
    return view('tabs.home');
})->middleware('auth');

Route::get('/home-content', function () {
    return view('tabs.home');
})->middleware('auth');


Route::post('/register', function (Request $request) {
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:8|confirmed',
    ]);
    
    $user = \App\Models\User::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => bcrypt($validated['password']),
    ]);
    
    return response()->json(['message' => 'User registered', 'user' => $user]);
});

// ==========================================
// PROTECTED ROUTES (Require Authentication)
// ==========================================
Route::middleware('auth:web')->get('/test-user', function (Request $request) {
    return response()->json(['user' => $request->user()]);
});
Route::middleware(['web', 'auth:web'])->group(function () {
    
    // ==========================================
    // USER INFO & GENERAL
    // ==========================================
    
    // Get authenticated user info
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // ==========================================
    // VENDOR MANAGEMENT (Available to both admin and vendors)
    // ==========================================
    
    Route::apiResource('vendors', VendorController::class);
    Route::apiResource('vendor-addresses', VendorAddressController::class);
    Route::apiResource('vendor-banks', VendorBankController::class);
    Route::apiResource('vendor-business-details', VendorBusinessDetailController::class);
    Route::apiResource('vendor-company-codes', VendorCompanyCodeController::class);
    Route::apiResource('vendor-contacts', VendorContactController::class);
    Route::apiResource('vendor-purchasing-orgs', VendorPurchasingOrgController::class);
    Route::apiResource('vendor-audit-logs', VendorAuditLogController::class);

    // ==========================================
    // PURCHASE ORDER WORKFLOW
    // ==========================================
    
    // Purchase Orders (CRUD)
    Route::apiResource('purchase-orders', PurchaseOrderController::class);
    
    // Purchase Order Actions
    Route::get('/purchase-orders/{id}/download-pdf', [PurchaseOrderController::class, 'downloadPdf']);
    Route::post('/purchase-orders/{id}/mark-delivered', [PurchaseOrderController::class, 'markDelivered']);
    Route::post('/purchase-orders/{id}/confirm-delivery', [PurchaseOrderController::class, 'confirmDelivery']);
    Route::post('/purchase-orders/{id}/acknowledge', [PurchaseOrderController::class, 'acknowledge']);
    Route::post('/purchase-orders/{id}/cancel', [PurchaseOrderController::class, 'cancel']);
    Route::get('/purchase-orders/{order}/items', [PurchaseOrderController::class, 'orderItems']);
    Route::post('/purchase-orders/{id}/make-delivery', [PurchaseOrderController::class, 'makeDelivery'])->name('purchase-orders.make-delivery');

     // DELIVERIES TAB (HTML content for AJAX tab)
    Route::get('/deliveries-content', [\App\Http\Controllers\DeliveryTabController::class, 'deliveriesTab'])
        ->name('vendor_deliveries.tab');

    // MAKE DELIVERY from acknowledged PO (creates Delivery + DeliveryItems)
    Route::post('/purchase-orders/{id}/make-delivery', [\App\Http\Controllers\DeliveryTabController::class, 'makeDelivery'])
        ->name('purchase-orders.make-delivery');

    // VENDOR: Save supplied quantity for a delivery item (locks after save)
    Route::post('/delivery-items/{id}/report-supplied', [\App\Http\Controllers\DeliveryTabController::class, 'reportSuppliedQuantity'])
        ->name('delivery-items.report-supplied');

    // ADMIN: Verify quantity received for a delivery item
    Route::post('/delivery-items/{id}/verify-received', [\App\Http\Controllers\DeliveryTabController::class, 'verifyReceivedQuantity'])
        ->middleware('role:admin')
        ->name('delivery-items.verify-received');

    // VENDOR: Add another (partial) delivery for a PO line item
    Route::post('/purchase-order-items/{po_item}/add-delivery', [\App\Http\Controllers\DeliveryTabController::class, 'addPartialDelivery'])
        ->name('purchase-order-items.add-delivery');

    //VENDOR OPEN LINE ITEMS FOR EACH PO
    Route::get('/deliveries/{orderId}/items', [\App\Http\Controllers\DeliveryTabController::class, 'deliveryOrderItems'])
    ->name('deliveries.order-items');
    
    //VENDOR ABLE TO SAVE BATCH OF DELIVERIES
    Route::post('/deliveries/{orderId}/batch-save', [\App\Http\Controllers\DeliveryTabController::class, 'batchSave'])->name('deliveries.batchSave');


    Route::get('/orders-content', [App\Http\Controllers\OrderTabController::class, 'ordersTab'])
    ->middleware('auth') 
    ->name('vendor_orders.tab');

    Route::get('/deliveries/{orderId}/new-row/{deliveryItemId}', [DeliveryTabController::class, 'newDeliveryRow']);

    // ==========================================
    // INVOICES
    // ==========================================
    
    Route::apiResource('invoices', InvoiceController::class);
    Route::get('/invoices/{id}/download-pdf', [InvoiceController::class, 'downloadPdf']);
    
    // ==========================================
    // PAYMENTS
    // ==========================================
    
    Route::apiResource('payments', PaymentController::class);
    Route::get('/payments/{id}/download-receipt', [PaymentController::class, 'downloadReceipt']);
    Route::post('/invoices/{id}/upload', [InvoiceController::class, 'upload'])->name('invoices.upload');

    //changes made by niveditha

    // ==========================================
    // REPORTS TAB
    // ==========================================

    Route::get('/reports-content', [ReportsController::class, 'reportsContent'])->name('reports.content');

    //changes end

    // ==========================================
    // ADMIN-ONLY ROUTES
    // ==========================================
    
    Route::middleware('role:admin')->group(function () {

        
        // Admin-only vendor management (if needed)
        Route::get('/admin/home-tab', function () {
               return view('tabs.admin_home');
        });
        Route::post('/admin/attach-po', [App\Http\Controllers\Admin\DashboardController::class, 'attachPO']) ->name('admin.attach-po');
        Route::post('/admin/confirm-po-attachment', [App\Http\Controllers\Admin\OrderController::class, 'confirmPOAttachment'])->name('admin.confirm-po-attachment');
        Route::get('/admin/vendors/all', [VendorController::class, 'adminIndex']);
        Route::get('/admin/purchase-orders/all', function () 
        {
                return \App\Models\PurchaseOrder::whereNotNull('po_pdf')
                    ->where('status', '!=', 'issued')
                    ->select('id', 'order_number', 'status', 'po_pdf')
                    ->orderByDesc('order_date')
                    ->get();
        });
        Route::post('/admin/remove-po-temp', [App\Http\Controllers\Admin\DashboardController::class, 'removePOTemp'])->name('admin.remove-po-temp');
        Route::post('/admin/issue-po', [App\Http\Controllers\Admin\DashboardController::class, 'issuePO'])->name('admin.issue-po');
        Route::post('/admin/issue-all-pos', [App\Http\Controllers\Admin\DashboardController::class, 'issueAllPOs'])->name('admin.issue-all-pos');
        Route::get('/admin/purchase-orders/missing-pdf', function () {
            return \App\Models\PurchaseOrder::whereNull('po_pdf')
                ->select('id','order_number', 'status', 'order_date', 'vendor_id')
                ->orderByDesc('order_date')
                ->get();
        });

        Route::get('admin/orders', [App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders.tab');
        Route::get('admin/orders/table', [App\Http\Controllers\Admin\OrderController::class, 'table'])->name('orders.table');
        Route::get('admin/orders/{order}/items', [App\Http\Controllers\Admin\OrderController::class, 'items'])->name('orders.items');
        Route::post('admin/orders/attach-po', [App\Http\Controllers\Admin\OrderController::class, 'attachPo'])->name('orders.attachPo');
        Route::post('admin/orders/attach-pdf-confirm', [App\Http\Controllers\Admin\OrderController::class, 'confirmPOAttachment'])->name('orders.attachPoConfirm');
        Route::post('admin/orders/remove-pdf', [App\Http\Controllers\Admin\OrderController::class, 'removePOTemp'])->name('orders.removePdf');
        Route::post('admin/orders/issue', [App\Http\Controllers\Admin\OrderController::class, 'issuePO'])->name('orders.issue');
        Route::post('admin/orders/issue-all', [App\Http\Controllers\Admin\OrderController::class, 'issueAllPOs'])->name('orders.issueAll');
        Route::post('admin/orders/attach-po-row', [App\Http\Controllers\Admin\OrderController::class, 'AttachPoPdftoRow'])->name('orders.attachPoRow');
        Route::post('admin/orders/remove-pdf-row', [App\Http\Controllers\Admin\OrderController::class, 'RemovePdffromRow'])->name('orders.removePdfRow');
        Route::post('admin/orders/issue-po-row', [App\Http\Controllers\Admin\OrderController::class, 'IssuePoRow'])->name('orders.issuePoRow');
        Route::post('admin/orders/release-all', [App\Http\Controllers\Admin\OrderController::class, 'releaseAll'])->name('orders.releaseAll');
        Route::get('admin/orders/{order}/split-view', [App\Http\Controllers\Admin\OrderController::class, 'splitView'])->name('orders.splitView');

            // All POs not released
            Route::get('/admin/purchase-orders/all-not-released', function () {
                $user = auth()->user();
                $query = \App\Models\PurchaseOrder::where('status', 'not verified')
                    ->select('id', 'order_number', 'status', 'order_date', 'vendor_id')
                    ->orderByDesc('order_date');

                if (!$user->is_superadmin) {
                    $companyCodes = \App\Models\AdminCompanyCode::where('admin_email', $user->email)->pluck('company_code');
                    $query->whereIn('amg_company_code', $companyCodes);
                }
                return $query->get();
            });

            // All POs not acknowledged
            Route::get('/admin/purchase-orders/all-not-acknowledged', function () {
                $user = auth()->user();
                $query = \App\Models\PurchaseOrder::where('status', 'issued')
                    ->select('id', 'order_number', 'status', 'order_date', 'vendor_id')
                    ->orderByDesc('order_date');

                if (!$user->is_superadmin) {
                    $companyCodes = \App\Models\AdminCompanyCode::where('admin_email', $user->email)->pluck('company_code');
                    $query->whereIn('amg_company_code', $companyCodes);
                }

                return $query->get();
            });

            // All POs not delivered
            Route::get('/admin/purchase-orders/all-not-delivered', function () {
              $user = auth()->user();
                $query = \App\Models\PurchaseOrder::where('status', '!=', 'delivered')
                    ->select('id', 'order_number', 'status', 'order_date', 'vendor_id')
                    ->orderByDesc('order_date');

                if (!$user->is_superadmin) {
                    $companyCodes = \App\Models\AdminCompanyCode::where('admin_email', $user->email)->pluck('company_code');
                    $query->whereIn('amg_company_code', $companyCodes);
                }

                return $query->get();
            });

            // All POs with partial delivery
            Route::get('/admin/purchase-orders/all-partial-delivery', function () {
                $user = auth()->user();
                $query = \App\Models\PurchaseOrder::where('status', 'partial delivery')
                    ->select('id', 'order_number', 'status', 'order_date', 'vendor_id')
                    ->orderByDesc('order_date');

                if (!$user->is_superadmin) {
                    $companyCodes = \App\Models\AdminCompanyCode::where('admin_email', $user->email)->pluck('company_code');
                    $query->whereIn('amg_company_code', $companyCodes);
                }

                return $query->get();
            });

        Route::get('/admin/deliveries-content', [\App\Http\Controllers\Admin\DeliveryController::class, 'deliveriesTab'])
        ->name('deliveries.tab');
        Route::post('/admin/assign-admin', [DashboardController::class, 'assignAdmin'])->name('admin.assignAdmin');
        Route::post('/admin/remove-admin', [DashboardController::class, 'removeAdmin'])->name('admin.removeAdmin');
        Route::get('/admin/profile', [DashboardController::class, 'profile'])->name('admin.profile');
        Route::post('/admin/profile/save/{admin}', [DashboardController::class, 'saveProfile'])->name('admin.profile.save');
        Route::post('/admin/change-password', [DashboardController::class, 'changePassword'])->name('admin.change.password');
    });
});
