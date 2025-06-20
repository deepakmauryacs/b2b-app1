<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\VendorController;
use App\Http\Controllers\Admin\VendorExportController;
use App\Http\Controllers\Admin\BuyerController;
use App\Http\Controllers\Admin\PendingProductController;
use App\Http\Controllers\Admin\ApprovedProductController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\RejectedProductController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Vendor\VendorDashboardController;
use App\Http\Controllers\Vendor\VendorProfileController;
use App\Http\Controllers\Vendor\VendorProductController;
use App\Http\Controllers\Vendor\VendorSubscriptionController;

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
        Route::get('search', [VendorController::class, 'search'])->name('search');
        Route::get('fetch', [VendorController::class, 'fetchVendors'])->name('fetch');
        Route::get('render-table', [VendorController::class, 'renderVendorsTable'])->name('render-table');
        Route::get('export-data', [VendorController::class, 'exportData'])->name('export-data');
    });

    Route::prefix('admin/vendor-exports')->name('admin.vendor-exports.')->group(function () {
        Route::get('/', [VendorExportController::class, 'index'])->name('index');
        Route::get('create', [VendorExportController::class, 'create'])->name('create');
        Route::post('store', [VendorExportController::class, 'store'])->name('store');
        Route::get('{id}/download', [VendorExportController::class, 'download'])->name('download');
        Route::delete('{id}', [VendorExportController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('admin/vendor-subscriptions')->name('admin.vendor-subscriptions.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\VendorSubscriptionController::class, 'index'])->name('index');
        Route::get('render-table', [App\Http\Controllers\Admin\VendorSubscriptionController::class, 'renderSubscriptionsTable'])->name('render-table');
        Route::get('create', [App\Http\Controllers\Admin\VendorSubscriptionController::class, 'create'])->name('create');
        Route::post('store', [App\Http\Controllers\Admin\VendorSubscriptionController::class, 'store'])->name('store');
        Route::get('{id}/edit', [App\Http\Controllers\Admin\VendorSubscriptionController::class, 'edit'])->name('edit');
        Route::put('{id}', [App\Http\Controllers\Admin\VendorSubscriptionController::class, 'update'])->name('update');
        Route::get('{id}', [App\Http\Controllers\Admin\VendorSubscriptionController::class, 'show'])->name('show');
        Route::get('{id}/print', [App\Http\Controllers\Admin\VendorSubscriptionController::class, 'show'])->name('print');
    });

    Route::prefix('admin/buyer-subscriptions')->name('admin.buyer-subscriptions.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\BuyerSubscriptionController::class, 'index'])->name('index');
        Route::get('render-table', [App\Http\Controllers\Admin\BuyerSubscriptionController::class, 'renderSubscriptionsTable'])->name('render-table');
        Route::get('create', [App\Http\Controllers\Admin\BuyerSubscriptionController::class, 'create'])->name('create');
        Route::post('store', [App\Http\Controllers\Admin\BuyerSubscriptionController::class, 'store'])->name('store');
        Route::get('{id}/edit', [App\Http\Controllers\Admin\BuyerSubscriptionController::class, 'edit'])->name('edit');
        Route::put('{id}', [App\Http\Controllers\Admin\BuyerSubscriptionController::class, 'update'])->name('update');
        Route::get('{id}', [App\Http\Controllers\Admin\BuyerSubscriptionController::class, 'show'])->name('show');
        Route::get('{id}/print', [App\Http\Controllers\Admin\BuyerSubscriptionController::class, 'show'])->name('print');
    });

    Route::prefix('admin/plans')->name('admin.plans.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\PlanController::class, 'index'])->name('index');
        Route::get('create', [App\Http\Controllers\Admin\PlanController::class, 'create'])->name('create');
        Route::post('store', [App\Http\Controllers\Admin\PlanController::class, 'store'])->name('store');
        Route::get('{plan}/edit', [App\Http\Controllers\Admin\PlanController::class, 'edit'])->name('edit');
        Route::put('{plan}', [App\Http\Controllers\Admin\PlanController::class, 'update'])->name('update');
    });

    Route::prefix('admin/roles')->name('admin.roles.')->group(function () {
        Route::get('list', [RoleController::class, 'index'])->name('index');
        Route::get('data', [RoleController::class, 'getRoles'])->name('data');
        Route::get('render-table', [RoleController::class, 'renderRolesTable'])->name('render-table');
        Route::get('create', [RoleController::class, 'create'])->name('create');
        Route::post('store', [RoleController::class, 'store'])->name('store');
        Route::get('edit/{id}', [RoleController::class, 'edit'])->name('edit');
        Route::put('update/{id}', [RoleController::class, 'update'])->name('update');
    });

    Route::prefix('admin/users')->name('admin.users.')->group(function () {
        Route::get('list', [UserController::class, 'index'])->name('index');
        Route::get('render-table', [UserController::class, 'renderUsersTable'])->name('render-table');
        Route::get('create', [UserController::class, 'create'])->name('create');
        Route::post('store', [UserController::class, 'store'])->name('store');
        Route::get('edit/{id}', [UserController::class, 'edit'])->name('edit');
        Route::put('update/{id}', [UserController::class, 'update'])->name('update');
    });

    Route::prefix('admin/buyers')->name('admin.buyers.')->group(function () {
        Route::get('list', [BuyerController::class, 'index'])->name('index');
        Route::get('data', [BuyerController::class, 'getBuyers'])->name('data');
        Route::get('render-table', [BuyerController::class, 'renderBuyersTable'])->name('render-table');
        Route::get('search', [BuyerController::class, 'search'])->name('search');
        Route::get('create', [BuyerController::class, 'create'])->name('create');
        Route::post('store', [BuyerController::class, 'store'])->name('store');
        Route::get('edit/{id}', [BuyerController::class, 'edit'])->name('edit');
        Route::put('update/{id}', [BuyerController::class, 'update'])->name('update');
        Route::post('update-profile-verification', [BuyerController::class, 'updateProfileVerification'])->name('update-profile-verification');
        Route::get('{id}', [BuyerController::class, 'show'])->name('show');
        Route::delete('delete/{id}', [BuyerController::class, 'destroy'])->name('delete');
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

    Route::get('/vendor/dashboard', [VendorDashboardController::class, 'index'])->name('vendor.dashboard');
    Route::get('/vendor/profile', [VendorProfileController::class, 'profile'])->name('vendor.profile.show');
    Route::post('profile/update', [VendorProfileController::class, 'update'])->name('vendor.profile.update');

    Route::get('/vendor/subscription', [VendorSubscriptionController::class, 'index'])->name('vendor.subscription.index');
    Route::post('/vendor/subscription', [VendorSubscriptionController::class, 'store'])->name('vendor.subscription.store');
    Route::get('/vendor/subscription/invoice', [VendorSubscriptionController::class, 'invoice'])->name('vendor.subscription.invoice');

    Route::prefix('vendor/products')->name('vendor.products.')->group(function () {
        Route::get('list', [VendorProductController::class, 'index'])->name('index');
        Route::get('approved', [VendorProductController::class, 'approved'])->name('approved');
        Route::get('pending', [VendorProductController::class, 'pending'])->name('pending');
        Route::get('rejected', [VendorProductController::class, 'rejected'])->name('rejected');
        Route::get('data', [VendorProductController::class, 'getProducts'])->name('data');
        Route::get('render-table', [VendorProductController::class, 'renderProductsTable'])->name('render-table');
        Route::get('create', [VendorProductController::class, 'create'])->name('create');
        Route::get('get-subcategories/{parentId}', [VendorProductController::class, 'getSubcategories'])->name('get-subcategories');
        Route::post('save', [VendorProductController::class, 'store'])->name('store');
        Route::get('{id}/edit', [VendorProductController::class, 'edit'])->name('edit');
        Route::put('update/{id}', [VendorProductController::class, 'update'])->name('update');
        Route::get('{id}', [VendorProductController::class, 'show'])->name('show');
        Route::delete('delete/{id}', [VendorProductController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('vendor/help-support')->name('vendor.help-support.')->group(function () {
        Route::get('list', [App\Http\Controllers\Vendor\VendorHelpSupportController::class, 'index'])->name('index');
        Route::get('render-table', [App\Http\Controllers\Vendor\VendorHelpSupportController::class, 'renderHelpsTable'])->name('render-table');
        Route::get('create', [App\Http\Controllers\Vendor\VendorHelpSupportController::class, 'create'])->name('create');
        Route::post('store', [App\Http\Controllers\Vendor\VendorHelpSupportController::class, 'store'])->name('store');
        Route::get('{id}', [App\Http\Controllers\Vendor\VendorHelpSupportController::class, 'show'])->name('show');
    });

    Route::prefix('vendor/inventory')->name('vendor.inventory.')->group(function () {
        Route::get('list', [App\Http\Controllers\Vendor\VendorInventoryController::class, 'index'])->name('index');
        Route::get('render-table', [App\Http\Controllers\Vendor\VendorInventoryController::class, 'renderInventoryTable'])->name('render-table');
        Route::post('update/{id}', [App\Http\Controllers\Vendor\VendorInventoryController::class, 'updateStock'])->name('update');
    });
});

Route::get('/buyer/dashboard', function () {
    // buyer dashboard view or controller
})->name('buyer.dashboard');
