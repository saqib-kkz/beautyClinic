<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Dashboard\DashboardController;
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

Route::prefix('/')->group(function () {
    Route::prefix('core')->name('core.')->group(function () {
        Route::prefix('module')->group(function () {
            Route::get('/', 'Core\ModuleController@list')->name('module-list');
            Route::get('create', 'Core\ModuleController@create')->name('module-create');
            Route::post('save', 'Core\ModuleController@save')->name('module-save');
            Route::get('edit/{uid}', 'Core\ModuleController@edit')->name('module-edit');
            Route::post('update', 'Core\ModuleController@update')->name('module-update');
            Route::get('permissions/{uid}', 'Core\ModuleController@permissions')->name('module-permissions');
            Route::post('savepermissions', 'Core\ModuleController@savepermissions')->name('module-savepermissions');
        });
    });
});

// Protected Routes - Require Authentication
Route::middleware(['auth', 'check.role'])->group(function () {
    
    // Dashboard - All authenticated users
    Route::controller(DashboardController::class)->group(function () {
        Route::get('dashboard', 'index')->name('dashboard');
    });
    
    // Admin Only Routes
    Route::middleware(['admin.only'])->group(function () {
        // User Management
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', 'UserController@index')->name('index');
            Route::get('create', 'UserController@create')->name('create');
            Route::post('store', 'UserController@store')->name('store');
            Route::get('{user}/edit', 'UserController@edit')->name('edit');
            Route::put('{user}', 'UserController@update')->name('update');
            Route::delete('{user}', 'UserController@destroy')->name('destroy');
        });
        
        // System Settings
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', 'SettingsController@index')->name('index');
            Route::put('update', 'SettingsController@update')->name('update');
        });
        
        // Advanced Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('financial', 'ReportController@financial')->name('financial');
            Route::get('inventory', 'ReportController@inventory')->name('inventory');
            Route::get('users', 'ReportController@users')->name('users');
        });
    });
    
    // Staff Routes (Admin + Staff can access)
    Route::middleware(['check.role:admin,staff'])->group(function () {
        
        // Client Management
        Route::prefix('clients')->name('clients.')->group(function () {
            Route::get('/', 'ClientController@index')->name('index');
            Route::get('create', 'ClientController@create')->name('create');
            Route::post('store', 'ClientController@store')->name('store');
            Route::get('{client}', 'ClientController@show')->name('show');
            Route::get('{client}/edit', 'ClientController@edit')->name('edit');
            Route::put('{client}', 'ClientController@update')->name('update');
        });
        
        // Product Management
        Route::prefix('products')->name('products.')->group(function () {
            Route::get('/', 'ProductController@index')->name('index');
            Route::get('create', 'ProductController@create')->name('create');
            Route::post('store', 'ProductController@store')->name('store');
            Route::get('{product}/edit', 'ProductController@edit')->name('edit');
            Route::put('{product}', 'ProductController@update')->name('update');
            Route::post('{product}/adjust-stock', 'ProductController@adjustStock')->name('adjust-stock');
        });
        
        // Treatment Management
        Route::prefix('treatments')->name('treatments.')->group(function () {
            Route::get('/', 'TreatmentController@index')->name('index');
            Route::get('create', 'TreatmentController@create')->name('create');
            Route::post('store', 'TreatmentController@store')->name('store');
            Route::get('{treatment}', 'TreatmentController@show')->name('show');
            Route::get('{treatment}/edit', 'TreatmentController@edit')->name('edit');
            Route::put('{treatment}', 'TreatmentController@update')->name('update');
        });
        
        // Invoice Management
        Route::prefix('invoices')->name('invoices.')->group(function () {
            Route::get('/', 'InvoiceController@index')->name('index');
            Route::get('create/{treatment?}', 'InvoiceController@create')->name('create');
            Route::post('store', 'InvoiceController@store')->name('store');
            Route::get('{invoice}', 'InvoiceController@show')->name('show');
            Route::get('{invoice}/pdf', 'InvoiceController@downloadPdf')->name('pdf');
            Route::get('{invoice}/print', 'InvoiceController@print')->name('print');
        });
        
        // Reports - Basic
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('treatments', 'ReportController@treatments')->name('treatments');
            Route::get('products', 'ReportController@products')->name('products');
        });
    });
});
