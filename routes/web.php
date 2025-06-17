<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\VendorController;
use App\Http\Controllers\Admin\BuyerController;
use App\Http\Controllers\Admin\PendingProductController;
use App\Http\Controllers\Admin\ApprovedProductController;
use App\Http\Controllers\Admin\RejectedProductController;
use App\Http\Controllers\Vendor\VendorProfileController;
use App\Http\Controllers\Vendor\VendorProductController;

Route::get('/clear-cache', function () {
    Artisan::call('optimize:clear');
    return 'All caches cleared!';
})->name('clear.cache');

Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('user/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('user/register', [RegisterController::class, 'register']);

Route::get('/refresh-math-captcha', function () {
    $a = rand(1, 9);
    $b = rand(1, 9);
    session(['captcha_result' => $a + $b, 'captcha_question' => "$a + $b"]);
    return response()->json(['question' => "$a + $b"]);
});

Route::middleware(['auth'])->group(function () {
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('admin.activity-logs.index');
    Route::get('/activity-logs/{id}', [ActivityLogController::class, 'show'])->name('admin.activity-logs.show');
    Route::get('/admin/activity-logs/data', [ActivityLogController::class, 'getActivityLogs'])->name('admin.activity-logs.data');

    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    Route::prefix('admin/categories')->name('admin.categories.')->group(function () {
        Route::get('list', [CategoryController::class, 'index'])->name('index');
        Route::get('data', [CategoryController::class, 'getCategories'])->name('data');
        Route::get('create', [CategoryController::class, 'create'])->name('create');
        Route::post('store', [CategoryController::class, 'store'])->name('store');
        Route::get('edit/{id}', [CategoryController::class, 'edit'])->name('edit');
        Route::put('update/{id}', [CategoryController::class, 'update'])->name('update');
        Route::delete('delete/{id}', [CategoryController::class, 'destroy'])->name('destroy');
        Route::get('get-subcategories/{parentId}', [CategoryController::class, 'getSubcategories'])->name('get-subcategories');
    });

    Route::prefix('admin/vendors')->name('admin.vendors.')->group(function () {
        Route::get('list', [VendorController::class, 'index'])->name('index');
        Route::get('data', [VendorController::class, 'getVendors'])->name('data');
        Route::get('create', [VendorController::class, 'create'])->name('create');
        Route::post('store', [VendorController::class, 'store'])->name('store');
        Route::get('edit/{id}', [VendorController::class, 'edit'])->name('edit');
        Route::put('update/{id}', [VendorController::class, 'update'])->name('update');
        Route::post('update-profile-verification', [VendorController::class, 'updateProfileVerification'])->name('update-profile-verification');
        Route::get('{id}', [VendorController::class, 'show'])->name('show');
        Route::get('export', [VendorController::class, 'exportVendors'])->name('export');
        Route::get('search', [VendorController::class, 'search'])->name('search');
        Route::get('fetch', [VendorController::class, 'fetchVendors'])->name('fetch');
        Route::get('render-table', [VendorController::class, 'renderVendorsTable'])->name('render-table');
    });

    Route::prefix('admin/buyers')->name('admin.buyers.')->group(function () {
        Route::get('list', [BuyerController::class, 'index'])->name('index');
        Route::get('data', [BuyerController::class, 'getBuyers'])->name('data');
        Route::get('create', [BuyerController::class, 'create'])->name('create');
        Route::post('store', [BuyerController::class, 'store'])->name('store');
        Route::get('edit/{id}', [BuyerController::class, 'edit'])->name('edit');
        Route::put('update/{id}', [BuyerController::class, 'update'])->name('update');
    });

    Route::prefix('admin/products')->group(function () {
        Route::get('/pending', [PendingProductController::class, 'index'])->name('admin.products.pending');
        Route::get('/pending/data', [PendingProductController::class, 'getPendingProducts'])->name('admin.products.pending.data');
        Route::get('pending/render-table', [PendingProductController::class, 'renderPendingProductsTable'])->name('admin.pending.products.render-table');
        Route::get('/pending/{product}', [PendingProductController::class, 'show'])->name('admin.products.pending.show');
        Route::put('/pending/{product}/approve', [PendingProductController::class, 'approve'])->name('admin.products.pending.approve');
        Route::put('/pending/{product}/reject', [PendingProductController::class, 'reject'])->name('admin.products.pending.reject');

        Route::get('/approved', [ApprovedProductController::class, 'index'])->name('admin.products.approved');
        Route::get('render-table', [ApprovedProductController::class, 'renderApprovedProductsTable'])->name('admin.products.render-table');
        Route::get('/approved/{id}', [ApprovedProductController::class, 'show'])->name('admin.products.approved.show');
        Route::post('/approved/{id}/revoke', [ApprovedProductController::class, 'revokeApproval'])->name('admin.products.approved.revoke');

        Route::get('/rejected', [RejectedProductController::class, 'index'])->name('admin.products.rejected');
        Route::get('/rejected/data', [RejectedProductController::class, 'getRejectedProducts'])->name('admin.products.rejected.data');
        Route::get('rejected/render-table', [RejectedProductController::class, 'renderRejectedProductsTable'])->name('admin.rejected.products.render-table');
        Route::get('/rejected/{id}', [RejectedProductController::class, 'show'])->name('admin.products.rejected.show');
        Route::post('/rejected/{id}/restore', [RejectedProductController::class, 'restore'])->name('admin.products.rejected.restore');
    });

    Route::get('/vendor/profile', [VendorProfileController::class, 'profile'])->name('vendor.profile.show');
    Route::post('profile/update', [VendorProfileController::class, 'update'])->name('vendor.profile.update');

    Route::prefix('vendor/products')->name('vendor.products.')->group(function () {
        Route::get('list', [VendorProductController::class, 'index'])->name('index');
        Route::get('data', [VendorProductController::class, 'getProducts'])->name('data');
        Route::get('create', [VendorProductController::class, 'create'])->name('create');
        Route::get('get-subcategories/{parentId}', [VendorProductController::class, 'getSubcategories'])->name('get-subcategories');
        Route::post('save', [VendorProductController::class, 'store'])->name('store');
        Route::get('{id}/edit', [VendorProductController::class, 'edit'])->name('edit');
        Route::put('update/{id}', [VendorProductController::class, 'update'])->name('update');
    });
});

Route::get('/buyer/dashboard', function () {
    // buyer dashboard view or controller
})->name('buyer.dashboard');
