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
use App\Http\Controllers\FinanceController;

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

     // General Services routes under UserController
    Route::get('/user/general-services/defects-and-complaints-form', [UserController::class, 'defectsAndComplaintsForm'])->name('user.general-services.defects_and_complaints_form');

    Route::get('/user/general-services/job-request-form', [UserController::class, 'jobRequestForm'])->name('user.general-services.job_request_form');
    Route::post('/user/general-services/job-request-form', [UserController::class, 'storeJobRequestForm'])->name('user.general-services.store_job_request_form');

    Route::get('/user/general-services/returned-unserviceable-form', [UserController::class, 'returnedUnserviceableForm'])->name('user.general-services.returned_unserviceable_form');
    Route::post('/user/general-services/returned-unserviceable-form', [UserController::class, 'storeReturnedUnserviceableForm'])->name('user.general-services.store_returned_unserviceable_form');

    Route::get('/user/general-services/gate-pass-form', [UserController::class, 'gatePassForm'])->name('user.general-services.gate_pass_form');
    Route::post('/user/general-services/gate-pass-form', [UserController::class, 'storeGatePassForm'])->name('user.general-services.store_gate_pass_form');

    Route::get('/user/general-services/inventory', [UserController::class, 'inventory'])->name('user.general-services.inventory');
    Route::get('/user/general-services/view_request', [UserController::class, 'viewRequest'])->name('user.general-services.view_request');

    // Route to get equipment details
    Route::get('/user/get-equipment-details', [UserController::class, 'getEquipmentDetails'])->name('user.getEquipmentDetails');

    Route::post('/user/general-services/store-defects-and-complaints-form', [UserController::class, 'storeDefectsAndComplaintsForm'])->name('user.general-services.store_defects_and_complaints_form');
    Route::post('/user/general-services/job-request', [UserController::class, 'storeJobRequest'])->name('user.general-services.store_job_request_form');
    
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

        Route::get('/{id}/unserviceable', [GssAdminController::class, 'unserviceableForm'])->name('serviceables.unserviceable_form');
        Route::put('/{id}/unserviceable', [GssAdminController::class, 'unserviceableUpdate'])->name('serviceables.unserviceable_update');
    });

    // Transferred Items route
    Route::match(['get', 'post'], '/gss/admin/transferred-items', [GssAdminController::class, 'transferredItems'])->name('gss.admin.transferred_items');

    // Unserviceable Items route
    Route::match(['get', 'post'], '/gss/admin/unserviceable-items', [GssAdminController::class, 'unserviceableItems'])->name('gss.admin.unserviceable_items');
    

    // Maintenance Ledger routes with sub-menu
    Route::prefix('gss/admin/maintenance')->group(function () {
        Route::get('/add-details', [GssAdminController::class, 'addMaintenanceDetails'])->name('gss.admin.add_maintenance_details');
        Route::post('/add-details', [GssAdminController::class, 'storeMaintenanceDetails'])->name('gss.admin.store_maintenance_details');
        Route::get('/property-numbers', [GssAdminController::class, 'getPropertyNumbers'])->name('gss.admin.property_numbers');
        Route::get('/ledger', [GssAdminController::class, 'ledger'])->name('gss.admin.ledger');
        Route::get('/ledger/search', [GssAdminController::class, 'searchLedger'])->name('gss.admin.ledger.search');
    });

    Route::get('/generate-pdf/{propertyNumber}', [GssAdminController::class, 'generatePdf'])->name('generate.pdf');
    Route::get('/preview-pdf/{propertyNumber}', [GssAdminController::class, 'previewPdf'])->name('preview.pdf');
    Route::get('/view-serviceable-template', [GssAdminController::class, 'viewServiceableTemplate'])->name('view-serviceable-template');
    Route::get('/get-sections/{div_name}', [GssAdminController::class, 'getSections']);

    // Other routes
    Route::get('/unserviceable', [GssAdminController::class, 'unserviceable'])->name('gss.admin.unserviceable');
    Route::get('/reconciliation', [GssAdminController::class, 'reconciliation'])->name('gss.admin.reconciliation');
});

// Routes for General Services User
Route::middleware(['auth', PreventBackHistory::class, CheckRoleAndOffice::class . ':user,Administrative Division,General Services Section'])->group(function () {
    Route::get('/gss/user/dashboard', [GssUserController::class, 'index'])->name('gss.user.dashboard');
});

// Routes for Finance
Route::middleware(['auth', PreventBackHistory::class, CheckRoleAndOffice::class . ':admin,Finance Division'])->group(function () {
    Route::get('/finance/dashboard', [FinanceController::class, 'index'])->name('finance.dashboard');
    Route::get('/finance/reconcile-items', [FinanceController::class, 'reconcileItems'])->name('finance.reconcile_items');
    Route::post('/finance/update-reconcile', [FinanceController::class, 'updateReconcile'])->name('finance.update_reconcile');
    Route::post('/finance/add-reconcile', [FinanceController::class, 'addReconcile'])->name('finance.add_reconcile');
    Route::get('/finance/equipment-near-end', [FinanceController::class, 'equipmentNearEnd'])->name('finance.equipment_near_end');
});

// Route for fetching sections accessible during registration
Route::get('/get-sections/{div_id}', [App\Http\Controllers\Auth\RegisterController::class, 'getSections'])->name('get.sections');

Route::get('register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [App\Http\Controllers\Auth\RegisterController::class, 'register'])->name('register.post');