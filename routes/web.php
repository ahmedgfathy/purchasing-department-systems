<?php

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\MarketController;
use App\Http\Controllers\OrderTypeController;
use App\Http\Controllers\ProposedMethodController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchasingController;
use App\Http\Controllers\RfqController;
use App\Http\Controllers\RfqLookupController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VendorController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
})->name('home');

Route::get('/language/{locale}', LocaleController::class)->name('language.switch');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

Route::post('/logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)
        ->middleware('permission:dashboard.view')
        ->name('dashboard');

    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/', [ProfileController::class, 'update'])->name('update');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password');
        Route::delete('/avatar', [ProfileController::class, 'destroyAvatar'])->name('avatar.destroy');
    });

    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])
            ->middleware('permission:users.view')->name('index');
        Route::get('/create', [UserController::class, 'create'])
            ->middleware('permission:users.create')->name('create');
        Route::post('/', [UserController::class, 'store'])
            ->middleware('permission:users.create')->name('store');
        Route::get('/{user}/edit', [UserController::class, 'edit'])
            ->middleware('permission:users.update')->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])
            ->middleware('permission:users.update')->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])
            ->middleware('permission:users.delete')->name('destroy');
    });

    Route::prefix('roles')->name('roles.')->group(function () {
        Route::get('/', [RoleController::class, 'index'])
            ->middleware('permission:roles.view')->name('index');
        Route::get('/create', [RoleController::class, 'create'])
            ->middleware('permission:roles.create')->name('create');
        Route::post('/', [RoleController::class, 'store'])
            ->middleware('permission:roles.create')->name('store');
        Route::get('/{role}/edit', [RoleController::class, 'edit'])
            ->middleware('permission:roles.update')->name('edit');
        Route::put('/{role}', [RoleController::class, 'update'])
            ->middleware('permission:roles.update')->name('update');
        Route::delete('/{role}', [RoleController::class, 'destroy'])
            ->middleware('permission:roles.delete')->name('destroy');
    });

    Route::get('/audit', [AuditLogController::class, 'index'])
        ->middleware('permission:audit.view')->name('audit.index');
    Route::get('/audit/{auditLog}', [AuditLogController::class, 'show'])
        ->middleware('permission:audit.view')->name('audit.show');

    Route::prefix('rfqs')->name('rfqs.')->group(function () {
        Route::get('/', [RfqController::class, 'index'])
            ->middleware('permission:rfq.view')->name('index');
        Route::get('/create', [RfqController::class, 'create'])
            ->middleware('permission:rfq.create')->name('create');
        Route::post('/', [RfqController::class, 'store'])
            ->middleware('permission:rfq.create')->name('store');
        Route::get('/{rfq}/edit', [RfqController::class, 'edit'])
            ->middleware('permission:rfq.update')->name('edit');
        Route::put('/{rfq}', [RfqController::class, 'update'])
            ->middleware('permission:rfq.update')->name('update');
        Route::delete('/{rfq}', [RfqController::class, 'destroy'])
            ->middleware('permission:rfq.delete')->name('destroy');
        Route::get('/{rfq}', [RfqController::class, 'show'])
            ->middleware('permission:rfq.view')->name('show');
        Route::post('/{rfq}/approve', [RfqController::class, 'approve'])
            ->middleware('permission:rfq.approve')->name('approve');
        Route::post('/{rfq}/reject', [RfqController::class, 'reject'])
            ->middleware('permission:rfq.approve')->name('reject');
        Route::post('/{rfq}/vendors', [RfqController::class, 'assignVendors'])
            ->middleware('permission:rfq.assign')->name('vendors');
    });

    Route::prefix('markets')->name('markets.')->group(function () {
        Route::get('/', [MarketController::class, 'index'])
            ->middleware('permission:markets.view')->name('index');
        Route::get('/create', [MarketController::class, 'create'])
            ->middleware('permission:markets.manage')->name('create');
        Route::post('/', [MarketController::class, 'store'])
            ->middleware('permission:markets.manage')->name('store');
        Route::get('/{market}/edit', [MarketController::class, 'edit'])
            ->middleware('permission:markets.manage')->name('edit');
        Route::put('/{market}', [MarketController::class, 'update'])
            ->middleware('permission:markets.manage')->name('update');
        Route::delete('/{market}', [MarketController::class, 'destroy'])
            ->middleware('permission:markets.manage')->name('destroy');
    });

    Route::prefix('order-types')->name('order_types.')->group(function () {
        Route::get('/', [OrderTypeController::class, 'index'])
            ->middleware('permission:order_types.view')->name('index');
        Route::get('/create', [OrderTypeController::class, 'create'])
            ->middleware('permission:order_types.manage')->name('create');
        Route::post('/', [OrderTypeController::class, 'store'])
            ->middleware('permission:order_types.manage')->name('store');
        Route::get('/{orderType}/edit', [OrderTypeController::class, 'edit'])
            ->middleware('permission:order_types.manage')->name('edit');
        Route::put('/{orderType}', [OrderTypeController::class, 'update'])
            ->middleware('permission:order_types.manage')->name('update');
        Route::delete('/{orderType}', [OrderTypeController::class, 'destroy'])
            ->middleware('permission:order_types.manage')->name('destroy');
    });

    Route::prefix('proposed-methods')->name('proposed_methods.')->group(function () {
        Route::get('/', [ProposedMethodController::class, 'index'])
            ->middleware('permission:proposed_methods.view')->name('index');
        Route::get('/create', [ProposedMethodController::class, 'create'])
            ->middleware('permission:proposed_methods.manage')->name('create');
        Route::post('/', [ProposedMethodController::class, 'store'])
            ->middleware('permission:proposed_methods.manage')->name('store');
        Route::get('/{proposedMethod}/edit', [ProposedMethodController::class, 'edit'])
            ->middleware('permission:proposed_methods.manage')->name('edit');
        Route::put('/{proposedMethod}', [ProposedMethodController::class, 'update'])
            ->middleware('permission:proposed_methods.manage')->name('update');
        Route::delete('/{proposedMethod}', [ProposedMethodController::class, 'destroy'])
            ->middleware('permission:proposed_methods.manage')->name('destroy');
    });

    Route::prefix('administration')->name('administration.')->group(function () {
        Route::get('/', [RfqLookupController::class, 'administration'])
            ->middleware('permission:departments.view,locations.view,priorities.view,currencies.view,units.view,markets.view,order_types.view,proposed_methods.view')->name('index');
        Route::get('/{type}', [RfqLookupController::class, 'index'])
            ->middleware('permission:departments.view,locations.view,priorities.view,currencies.view,units.view')->name('lookup.index');
        Route::get('/{type}/create', [RfqLookupController::class, 'create'])
            ->middleware('permission:departments.manage,locations.manage,priorities.manage,currencies.manage,units.manage')->name('lookup.create');
        Route::post('/{type}', [RfqLookupController::class, 'store'])
            ->middleware('permission:departments.manage,locations.manage,priorities.manage,currencies.manage,units.manage')->name('lookup.store');
        Route::get('/{type}/{id}/edit', [RfqLookupController::class, 'edit'])
            ->middleware('permission:departments.manage,locations.manage,priorities.manage,currencies.manage,units.manage')->name('lookup.edit');
        Route::put('/{type}/{id}', [RfqLookupController::class, 'update'])
            ->middleware('permission:departments.manage,locations.manage,priorities.manage,currencies.manage,units.manage')->name('lookup.update');
        Route::delete('/{type}/{id}', [RfqLookupController::class, 'destroy'])
            ->middleware('permission:departments.manage,locations.manage,priorities.manage,currencies.manage,units.manage')->name('lookup.destroy');
    });

    Route::prefix('vendors')->name('vendors.')->group(function () {
        Route::get('/', [VendorController::class, 'index'])
            ->middleware('permission:vendors.view')->name('index');
        Route::get('/create', [VendorController::class, 'create'])
            ->middleware('permission:vendors.manage')->name('create');
        Route::post('/', [VendorController::class, 'store'])
            ->middleware('permission:vendors.manage')->name('store');
        Route::get('/activities', [VendorController::class, 'activities'])
            ->middleware('permission:vendors.view')->name('activities');
        Route::get('/{vendor}', [VendorController::class, 'show'])
            ->middleware('permission:vendors.view')->name('show');
        Route::get('/{vendor}/edit', [VendorController::class, 'edit'])
            ->middleware('permission:vendors.manage')->name('edit');
        Route::put('/{vendor}', [VendorController::class, 'update'])
            ->middleware('permission:vendors.manage')->name('update');
        Route::delete('/{vendor}', [VendorController::class, 'destroy'])
            ->middleware('permission:vendors.manage')->name('destroy');
        Route::post('/{vendor}/services', [VendorController::class, 'storeService'])
            ->middleware('permission:vendors.manage')->name('services.store');
        Route::put('/{vendor}/services/{service}', [VendorController::class, 'updateService'])
            ->middleware('permission:vendors.manage')->name('services.update');
        Route::delete('/{vendor}/services/{service}', [VendorController::class, 'destroyService'])
            ->middleware('permission:vendors.manage')->name('services.destroy');
    });

    Route::prefix('purchasing')->name('purchasing.')->group(function () {
        Route::get('/local', [PurchasingController::class, 'local'])
            ->middleware('permission:purchases.local.view')->name('local');
        Route::get('/foreign', [PurchasingController::class, 'foreign'])
            ->middleware('permission:purchases.foreign.view')->name('foreign');
        Route::get('/market', [PurchasingController::class, 'market'])
            ->middleware('permission:market.view')->name('market');
    });
});
