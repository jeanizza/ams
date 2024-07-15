<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckRole;
use App\Http\Middleware\CheckRoleAndOffice;
use App\Http\Middleware\PreventBackHistory;
use App\Http\Controllers\GssAdminController;
use App\Http\Controllers\UserController;

Auth::routes();

//login page
Route::get('/', function () {
    return redirect('/login');
});

// Routes accessible to all authenticated users
Route::middleware(['auth', PreventBackHistory::class])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
});

// Routes accessible only to admins
Route::middleware(['auth', CheckRole::class . ':admin', PreventBackHistory::class])->group(function () {
    Route::get('/admin/dashboard', [App\Http\Controllers\AdminController::class, 'index'])->name('admin.dashboard');
});

// Routes accessible only to regular users
Route::middleware(['auth', CheckRole::class . ':user', PreventBackHistory::class])->group(function () {
    Route::get('/user/dashboard', [App\Http\Controllers\UserController::class, 'index'])->name('user.dashboard');
});

// Routes for Procurement Admin
Route::middleware(['auth', PreventBackHistory::class, CheckRoleAndOffice::class . ':admin,procurement services section'])->group(function () {
    Route::get('/procurement/admin/dashboard', [App\Http\Controllers\ProcurementAdminController::class, 'index'])->name('procurement.admin.dashboard');
});

// Routes for Procurement User
Route::middleware(['auth', PreventBackHistory::class, CheckRoleAndOffice::class . ':user,procurement services section'])->group(function () {
    Route::get('/procurement/user/dashboard', [App\Http\Controllers\ProcurementUserController::class, 'index'])->name('procurement.user.dashboard');
});

// Routes for General Services Admin
Route::middleware(['auth', PreventBackHistory::class, CheckRoleAndOffice::class . ':admin,general services section'])->group(function () {
    Route::get('/gss/admin/dashboard', [App\Http\Controllers\GssAdminController::class, 'index'])->name('gss.admin.dashboard');

    // Serviceable routes with sub-menu
    Route::prefix('gss/admin/serviceable')->group(function () {
        Route::get('/add-record', [GssAdminController::class, 'add_record'])->name('gss.admin.add_record');
        Route::get('/transfer-property', [GssAdminController::class, 'transfer_property'])->name('gss.admin.transfer_property');
    });

    // Add this route to handle the form submission
    Route::post('/gss/admin/add-record', [GssAdminController::class, 'storeAddRecord'])->name('gss.admin.store_add_record');

    Route::get('/sections/{div_id}', [GssAdminController::class, 'getSections'])->name('gss.admin.get_sections');

    // Other routes
    Route::get('/unserviceable', [GssAdminController::class, 'unserviceable'])->name('gss.admin.unserviceable');
    Route::get('/maintenance_ledger', [GssAdminController::class, 'maintenanceLedger'])->name('gss.admin.maintenance_ledger');
    Route::get('/reconciliation', [GssAdminController::class, 'reconciliation'])->name('gss.admin.reconciliation');
});

// Routes for General Services User
Route::middleware(['auth', PreventBackHistory::class, CheckRoleAndOffice::class . ':user,general services section'])->group(function () {
    Route::get('/gss/user/dashboard', [App\Http\Controllers\GssUserController::class, 'index'])->name('gss.user.dashboard');
});

// Routes for Accounting
Route::middleware(['auth', PreventBackHistory::class, CheckRoleAndOffice::class . ':admin,accounting section'])->group(function () {
    Route::get('/accounting/dashboard', [App\Http\Controllers\AccountingSectionController::class, 'index'])->name('accounting.dashboard');
});
