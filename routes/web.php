<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckRole;
use App\Http\Middleware\CheckRoleAndOffice;
use App\Http\Middleware\PreventBackHistory;
use App\Http\Controllers\GssAdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProcurementAdminController;
use App\Http\Controllers\ProcurementUserController;
use App\Http\Controllers\GssUserController;
use App\Http\Controllers\AccountingSectionController;
use App\Http\Controllers\AccountingUserController;

Auth::routes();

//login page
Route::get('/', function () {
    return redirect('/login');
});

// Routes accessible to all authenticated users
Route::middleware(['auth', PreventBackHistory::class])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

// Routes accessible only to admins
Route::middleware(['auth', CheckRole::class . ':admin', PreventBackHistory::class])->group(function () {
    Route::get('/admin/dashboard', [App\Http\Controllers\AdminController::class, 'index'])->name('admin.dashboard');
});

// Routes accessible only to regular users
Route::middleware(['auth', CheckRole::class . ':user', PreventBackHistory::class])->group(function () {
    Route::get('/user/dashboard', [UserController::class, 'index'])->name('user.dashboard');
});

// Routes for Procurement Admin
Route::middleware(['auth', PreventBackHistory::class, CheckRoleAndOffice::class . ':admin,Administrative Division,Procurement Services Section'])->group(function () {
    Route::get('/procurement/admin/dashboard', [ProcurementAdminController::class, 'index'])->name('procurement.admin.dashboard');
});

// Routes for Procurement User
Route::middleware(['auth', PreventBackHistory::class, CheckRoleAndOffice::class . ':user,Administrative Division,Procurement Services Section'])->group(function () {
    Route::get('/procurement/user/dashboard', [ProcurementUserController::class, 'index'])->name('procurement.user.dashboard');
});

// Routes for General Services Admin
Route::middleware(['auth', PreventBackHistory::class, CheckRoleAndOffice::class . ':admin,Administrative Division,General Services Section'])->group(function () {
    Route::get('/gss/admin/dashboard', [GssAdminController::class, 'index'])->name('gss.admin.dashboard');

    // Serviceable routes with sub-menu
    Route::prefix('gss/admin/serviceable')->group(function () {
        Route::get('/add-record', [GssAdminController::class, 'add_record'])->name('gss.admin.add_record');
        Route::post('/add-record', [GssAdminController::class, 'storeAddRecord'])->name('gss.admin.store_add_record');
        Route::get('/list-serviceable', [GssAdminController::class, 'list_serviceable'])->name('gss.admin.list_serviceable');
        
        Route::get('/{id}/update', [GssAdminController::class, 'updateServiceableForm'])->name('serviceables.update_serviceable');
        Route::put('/{id}/update', [GssAdminController::class, 'updateServiceable'])->name('serviceables.update');

        Route::get('/{id}/transfer', [GssAdminController::class, 'transferServiceableForm'])->name('serviceables.transfer_serviceable');
        Route::put('/{id}/transfer', [GssAdminController::class, 'transferServiceable'])->name('serviceables.transfer');

        Route::put('/serviceables/{serviceable}/unserviceable', [GssAdminController::class, 'unserviceableUpdate'])->name('serviceables.unserviceable');


        Route::get('/{id}/unserviceable', [GssAdminController::class, 'unserviceableForm'])->name('serviceables.unserviceable_form');
        Route::put('/{id}/unserviceable', [GssAdminController::class, 'unserviceableUpdate'])->name('serviceables.unserviceable_update');
    });

    Route::get('/generate-pdf/{propertyNumber}', [GssAdminController::class, 'generatePdf'])->name('generate.pdf');
    Route::get('/view-serviceable-template', [GssAdminController::class, 'viewServiceableTemplate'])->name('view-serviceable-template');
    Route::get('/get-sections/{div_name}', [GssAdminController::class, 'getSections']);
    

    // Other routes
    Route::get('/unserviceable', [GssAdminController::class, 'unserviceable'])->name('gss.admin.unserviceable');
    Route::get('/maintenance_ledger', [GssAdminController::class, 'maintenanceLedger'])->name('gss.admin.maintenance_ledger');
    Route::get('/reconciliation', [GssAdminController::class, 'reconciliation'])->name('gss.admin.reconciliation');
});

// Routes for General Services User
Route::middleware(['auth', PreventBackHistory::class, CheckRoleAndOffice::class . ':user,Administrative Division,General Services Section'])->group(function () {
    Route::get('/gss/user/dashboard', [GssUserController::class, 'index'])->name('gss.user.dashboard');
});

// Routes for Accounting
Route::middleware(['auth', PreventBackHistory::class, CheckRoleAndOffice::class . ':admin,Finance Division'])->group(function () {
    Route::get('/accounting/dashboard', [App\Http\Controllers\AccountingSectionController::class, 'index'])->name('accounting.dashboard');
});

// Route for fetching sections accessible during registration
Route::get('/get-sections/{div_id}', [GssAdminController::class, 'getSections'])->name('get.sections');

Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [RegisterController::class, 'register'])->name('register.post');