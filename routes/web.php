<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckRole;
use App\Http\Middleware\CheckRoleAndOffice;
use App\Http\Middleware\PreventBackHistory;


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
        Route::get('/par', [App\Http\Controllers\GssAdminController::class, 'par'])->name('gss.admin.par');
        Route::get('/ics', [App\Http\Controllers\GssAdminController::class, 'ics'])->name('gss.admin.ics');
        Route::get('/ptr', [App\Http\Controllers\GssAdminController::class, 'ptr'])->name('gss.admin.ptr');
        Route::get('/itr', [App\Http\Controllers\GssAdminController::class, 'itr'])->name('gss.admin.itr');
    });

    // Other routes
    Route::get('/unserviceable', [App\Http\Controllers\GssAdminController::class, 'unserviceable'])->name('gss.admin.unserviceable');
    Route::get('/maintenance_ledger', [App\Http\Controllers\GssAdminController::class, 'maintenanceLedger'])->name('gss.admin.maintenance_ledger');
    Route::get('/reconciliation', [App\Http\Controllers\GssAdminController::class, 'reconciliation'])->name('gss.admin.reconciliation');
});

// Routes for General Services User
Route::middleware(['auth', PreventBackHistory::class, CheckRoleAndOffice::class . ':user,general services section'])->group(function () {
    Route::get('/gss/user/dashboard', [App\Http\Controllers\GssUserController::class, 'index'])->name('gss.user.dashboard');
});

// Routes for Accounting
Route::middleware(['auth', PreventBackHistory::class, CheckRoleAndOffice::class . ':admin,accounting section'])->group(function () {
    Route::get('/accounting/dashboard', [App\Http\Controllers\AccountingSectionController::class, 'index'])->name('accounting.dashboard');
});