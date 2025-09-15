<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\TreatmentsController;
use App\Http\Controllers\InvoicesController;
use App\Http\Controllers\ClinicProfileController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TreatmentTypeController;
use App\Http\Controllers\Auth\LoginController as AuthLoginController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::controller(LoginController::class)->group(function () {
    Route::get('/', 'Login')->name('get-login');
    Route::get('login', 'Login')->name('get-login22');
    Route::post('login', 'postLogin')->name('login');
    Route::any('logout', 'logout')->name('logout');
});

// Test route to check database connection
Route::get('/test-db', function() {
    try {
        $products = \App\Models\Products::count();
        return response()->json(['status' => 'success', 'products_count' => $products]);
    } catch (\Exception $e) {
        return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
    }
});

// Product Management (temporarily without middleware for testing)
Route::prefix('products')->name('products.')->group(function () {
    Route::get('/', [ProductsController::class, 'index'])->name('index');
    Route::post('store', [ProductsController::class, 'store'])->name('store');
    Route::post('fetch', [ProductsController::class, 'fetch'])->name('fetch');
    Route::get('{product}/edit', [ProductsController::class, 'edit'])->name('edit');
    Route::put('{product}', [ProductsController::class, 'update'])->name('update');
    Route::post('{product}/adjust-stock', [ProductsController::class, 'adjustStock'])->name('adjust-stock');
});







// Protected Routes - Require Authentication
Route::middleware(['auth', 'check.role'])->group(function () {
    
    // Dashboard - All authenticated users
    Route::controller(DashboardController::class)->group(function () {
        Route::get('dashboard', 'index')->name('dashboard');
    });
    

    
    // Staff Routes (Admin + Staff can access)
    Route::middleware(['check.role:admin,staff'])->group(function () {
        
        // Client Management
        Route::prefix('clients')->name('clients.')->group(function () {
            Route::get('/', [ClientController::class, 'index'])->name('index');
            Route::post('store', [ClientController::class, 'store'])->name('store');
            Route::post('fetch', [ClientController::class, 'fetch'])->name('fetch');
            Route::get('{client}', [ClientController::class, 'show'])->name('show');
            Route::get('{client}/edit', [ClientController::class, 'edit'])->name('edit');
            Route::put('{client}', [ClientController::class, 'update'])->name('update');
        });
        

        
        // Treatment Management
        Route::prefix('treatments')->name('treatments.')->group(function () {
            Route::get('/', [TreatmentsController::class, 'index'])->name('index');
            Route::get('create', [TreatmentsController::class, 'create'])->name('create');
            Route::post('store', [TreatmentsController::class, 'store'])->name('store');
            Route::get('{treatment}', [TreatmentsController::class, 'show'])->name('show');
            Route::get('{treatment}/edit', [TreatmentsController::class, 'edit'])->name('edit');
            Route::put('{treatment}', [TreatmentsController::class, 'update'])->name('update');
            Route::delete('{treatment}', [TreatmentsController::class, 'destroy'])->name('destroy');
            
            // API routes
            Route::get('api/products', [TreatmentsController::class, 'getProducts'])->name('api.products');
            Route::get('api/clients/{client}/treatments', [TreatmentsController::class, 'getClientTreatments'])->name('api.client-treatments');
            Route::get('api/treatment-types', [TreatmentsController::class, 'getTreatmentTypeSuggestions'])->name('api.treatment-types');
            Route::get('api/treatment-types/{id}/details', [TreatmentsController::class, 'getTreatmentTypeDetails'])->name('api.treatment-type-details');
            Route::get('api/{treatment}/print', [TreatmentsController::class, 'getTreatmentForPrint'])->name('getTreatmentForPrint');
        });

        // Treatment Types Management
        Route::prefix('treatment-types')->name('treatment-types.')->group(function () {
            Route::get('/', [TreatmentTypeController::class, 'index'])->name('index');
            Route::post('store', [TreatmentTypeController::class, 'store'])->name('store');
            Route::get('{treatmentType}/edit', [TreatmentTypeController::class, 'edit'])->name('edit');
            Route::put('{treatmentType}', [TreatmentTypeController::class, 'update'])->name('update');
            Route::post('{treatmentType}/toggle-status', [TreatmentTypeController::class, 'toggleStatus'])->name('toggleStatus');
            Route::delete('{treatmentType}', [TreatmentTypeController::class, 'destroy'])->name('destroy');
        });
        
        // Staff Management (Admin Only)
        Route::middleware(['check.role:admin'])->group(function () {
            Route::prefix('staff')->name('staff.')->group(function () {
                Route::get('/', [App\Http\Controllers\StaffController::class, 'index'])->name('index');
                Route::get('create', [App\Http\Controllers\StaffController::class, 'create'])->name('create');
                Route::post('store', [App\Http\Controllers\StaffController::class, 'store'])->name('store');
                Route::get('{staff}', [App\Http\Controllers\StaffController::class, 'show'])->name('show');
                Route::get('{staff}/edit', [App\Http\Controllers\StaffController::class, 'edit'])->name('edit');
                Route::put('{staff}', [App\Http\Controllers\StaffController::class, 'update'])->name('update');
                Route::delete('{staff}', [App\Http\Controllers\StaffController::class, 'destroy'])->name('destroy');
                Route::post('{staff}/toggle-status', [App\Http\Controllers\StaffController::class, 'toggleStatus'])->name('toggleStatus');
            });

            // Clinic Profile Management
            Route::prefix('clinic-profile')->name('clinic-profile.')->group(function () {
                Route::get('/', [App\Http\Controllers\ClinicProfileController::class, 'index'])->name('index');
                Route::get('edit', [App\Http\Controllers\ClinicProfileController::class, 'edit'])->name('edit');
                Route::post('update', [App\Http\Controllers\ClinicProfileController::class, 'update'])->name('update');
            });

        });

        // User Profile Management (All authenticated users)
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [ProfileController::class, 'index'])->name('index');
            Route::get('edit', [ProfileController::class, 'edit'])->name('edit');
            Route::post('update', [ProfileController::class, 'update'])->name('update');
        });

        // Invoice Management
        Route::prefix('invoices')->name('invoices.')->group(function () {
            Route::get('/', [InvoicesController::class, 'index'])->name('index');
            Route::get('create/{treatment?}', [InvoicesController::class, 'create'])->name('create');
            Route::post('store', [InvoicesController::class, 'store'])->name('store');
            Route::get('{invoice}', [InvoicesController::class, 'show'])->name('show');
            Route::get('{invoice}/pdf', [InvoicesController::class, 'downloadPdf'])->name('pdf');
            Route::get('{invoice}/print', [InvoicesController::class, 'print'])->name('print');
        });

        // Reports Management
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [App\Http\Controllers\ReportsController::class, 'index'])->name('index');
            Route::get('invoice', [App\Http\Controllers\ReportsController::class, 'invoiceReport'])->name('invoice');
            Route::get('invoice/export', [App\Http\Controllers\ReportsController::class, 'exportInvoiceReport'])->name('invoice.export');
            Route::get('product', [App\Http\Controllers\ReportsController::class, 'productReport'])->name('product');
            Route::get('product/export', [App\Http\Controllers\ReportsController::class, 'exportProductReport'])->name('product.export');
        });
    });
});
