<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
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

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// ==========================================
// PUBLIC ROUTES (No Authentication Required)
// ==========================================

// User Registration
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

// User Login (using AuthController for better organization)
Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login']);

// Alternative simple login (fallback if AuthController doesn't exist)
Route::post('/login-simple', function (Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);
    
    if (!auth()->attempt($credentials)) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }
    
    $user = auth()->user();
    $token = $user->createToken('api-token')->plainTextToken;
    return response()->json(['token' => $token, 'user' => $user]);
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
    
    // ==========================================
    // DELIVERIES
    // ==========================================
    
    Route::apiResource('deliveries', DeliveryController::class);
    Route::get('/deliveries/{id}/download-grn', [DeliveryController::class, 'downloadGrn']);
    
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

    // ==========================================
    // ADMIN-ONLY ROUTES
    // ==========================================
    
    Route::middleware('role:admin')->group(function () {
        
        // Admin Dashboard
        Route::get('/admin/dashboard', function () {
            return response()->json([
                'message' => 'Welcome to the Admin Dashboard!',
                'access_level' => 'Full administrative privileges'
            ]);
        });
        
        // Admin-only vendor management (if needed)
        Route::get('/admin/vendors/all', [VendorController::class, 'adminIndex']);
        Route::get('/admin/purchase-orders/all', [PurchaseOrderController::class, 'adminIndex']);
        
        // Add more admin-only routes here as needed:
        // Route::get('/admin/users', [AdminController::class, 'listUsers']);
        // Route::get('/admin/reports', [AdminController::class, 'generateReports']);
    });
});
