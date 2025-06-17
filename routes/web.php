<?php
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


use App\Http\Controllers\Vendor\VendorDashboardController;
use App\Http\Controllers\Vendor\VendorProfileController;
use App\Http\Controllers\Vendor\VendorProductController;

use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Artisan;

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
    });


   Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    Route::get('admin/categories/list', [CategoryController::class, 'index'])->name('admin.categories.index');
    Route::get('admin/categories/data', [CategoryController::class, 'getCategories'])->name('admin.categories.data');
    Route::get('admin/categories/create', [CategoryController::class, 'create'])->name('admin.categories.create');
    Route::post('admin/categories/store', [CategoryController::class, 'store'])->name('admin.categories.store');
    Route::get('admin/categories/edit/{id}', [CategoryController::class, 'edit'])->name('admin.categories.edit');
    Route::put('admin/categories/update/{id}', [CategoryController::class, 'update'])->name('admin.categories.update');
    Route::delete('admin/categories/delete/{id}', [CategoryController::class, 'destroy'])->name('admin.categories.destroy');
    Route::get('admin/get-subcategories/{parentId}', [CategoryController::class, 'getSubcategories'])->name('admin.get-subcategories');
    
    

    Route::get('admin/vendors/list', [VendorController::class, 'index'])->name('admin.vendors.index');
    Route::get('/vendors/data', [VendorController::class, 'getVendors'])->name('admin.vendors.data');
    Route::get('admin/vendors/create', [VendorController::class, 'create'])->name('admin.vendors.create');
    Route::post('admin/vendors/store', [VendorController::class, 'store'])->name('admin.vendors.store');
    Route::get('admin/vendors/edit/{id}', [VendorController::class, 'edit'])->name('admin.vendors.edit');
    Route::put('admin/vendors/update/{id}', [VendorController::class, 'update'])->name('admin.vendors.update');

    Route::post('admin/vendors/update-profile-verification', [VendorController::class, 'updateProfileVerification'])->name('admin.vendors.update-profile-verification');

    Route::get('admin/vendors/{id}', [VendorController::class, 'show'])->name('admin.vendors.show');
    Route::get('vendors/export', [VendorController::class, 'exportVendors'])->name('admin.vendors.export');

    Route::post('/admin/vendors/export/start', [VendorController::class, 'startExport'])->name('admin.vendors.export.start');
    Route::get('/admin/vendors/export/progress', [VendorController::class, 'getProgress'])->name('admin.vendors.export.progress');
    Route::get('/admin/vendors/export/download', [VendorController::class, 'download'])->name('admin.vendors.export.download');

    Route::get('/admin/vendor-search', [VendorController::class, 'search'])->name('admin.vendor.search');

    // This replaces the DataTables 'getVendors' route
    Route::get('vendors/fetch', [VendorController::class, 'fetchVendors'])->name('admin.vendors.fetch');
     // THIS IS THE ROUTE THAT NEEDS TO BE CORRECTLY DEFINED
    Route::get('vendors/render-table', [VendorController::class, 'renderVendorsTable'])->name('admin.vendors.render-table');





    Route::get('admin/buyers/list', [BuyerController::class, 'index'])->name('admin.buyers.index');
    Route::get('/buyers/data', [BuyerController::class, 'getBuyers'])->name('admin.buyers.data');
    Route::get('admin/buyers/create', [BuyerController::class, 'create'])->name('admin.buyers.create');
    Route::post('admin/buyers/store', [BuyerController::class, 'store'])->name('admin.buyers.store');
    Route::get('admin/buyers/edit/{id}', [BuyerController::class, 'edit'])->name('admin.buyers.edit');
    Route::put('admin/buyers/update/{id}', [BuyerController::class, 'update'])->name('admin.buyers.update');
   

    // Pending Products Routes
    Route::prefix('admin')->group(function() {
    // Pending Products Routes
    Route::get('/products/pending', [PendingProductController::class, 'index'])->name('admin.products.pending');
    Route::get('/products/pending/data', [PendingProductController::class, 'getPendingProducts'])->name('admin.products.pending.data');
    Route::get('products/pending/render-table', [PendingProductController::class, 'renderPendingProductsTable'])->name('admin.pending.products.render-table');


    Route::get('/products/pending/{product}', [PendingProductController::class, 'show'])->name('admin.products.pending.show');
    
    // Approval Routes - using PUT method as it's modifying resource state
    Route::put('/products/pending/{product}/approve', [PendingProductController::class, 'approve'])
        ->name('admin.products.pending.approve');
    Route::put('/products/pending/{product}/reject', [PendingProductController::class, 'reject'])
        ->name('admin.products.pending.reject');
    });





    Route::get('approved-products', [ApprovedProductController::class, 'index'])->name('admin.products.approved');


    Route::get('products/render-table', [ApprovedProductController::class, 'renderApprovedProductsTable'])->name('admin.products.render-table');


    Route::get('approved-products/{id}', [ApprovedProductController::class, 'show'])->name('admin.products.approved.show');
    Route::post('approved-products/{id}/revoke', [ApprovedProductController::class, 'revokeApproval'])->name('admin.products.approved.revoke');


    
    // Rejected products routes
    Route::get('rejected-products', [RejectedProductController::class, 'index'])->name('admin.products.rejected');
    Route::get('rejected-products/data', [RejectedProductController::class, 'getRejectedProducts'])->name('admin.products.rejected.data');
    Route::get('products/rejected/render-table', [RejectedProductController::class, 'renderRejectedProductsTable'])->name('admin.rejected.products.render-table');
    Route::get('rejected-products/{id}', [RejectedProductController::class, 'show'])->name('admin.products.rejected.show');

    Route::post('rejected-products/{id}/restore', [RejectedProductController::class, 'restore'])->name('admin.products.rejected.restore');
    


    



  








// Route::middleware(['auth'])->group(function () {


         


    Route::get('/vendor/profile', [VendorProfileController::class, 'profile'])->name('vendor.profile.show');
    Route::post('profile/update', [VendorProfileController::class, 'update'])->name('vendor.profile.update');

    

    Route::get('vendor/products/list', [VendorProductController::class, 'index'])->name('vendor.products.index');
    Route::get('vendor/products/data', [VendorProductController::class, 'getProducts'])
         ->name('vendor.products.data');

    Route::get('vendor/product/create', [VendorProductController::class, 'create'])->name('vendor.products.create');
    Route::get('vendor/get-subcategories/{parentId}', [VendorProductController::class, 'getSubcategories'])->name('vendor.get-subcategories');
    Route::post('vendor/product/save', [VendorProductController::class, 'store'])->name('vendor.products.store');
    Route::get('vendor/product/{id}/edit', [VendorProductController::class, 'edit'])->name('vendor.products.edit');
    Route::put('vendor/product/update/{id}', [VendorProductController::class, 'update'])->name('vendor.products.update');


    

  

// });



Route::get('/buyer/dashboard', function () {
    // buyer dashboard view or controller
})->name('buyer.dashboard');
