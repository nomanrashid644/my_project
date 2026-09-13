<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CampaignBrowseController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\AdminWalletController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AiChatController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\AdminDeliveryController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login.store');
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
    Route::get('/register', [AuthController::class, 'showRegistration'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::middleware('role:admin,pharmacy_staff')->group(function () {
        Route::resource('medicines', MedicineController::class)->only(['create', 'store', 'edit', 'update', 'destroy']);
    });
    Route::resource('medicines', MedicineController::class)->only(['index', 'show']);
    Route::middleware('role:customer')->group(function () {
        Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
        Route::post('/cart/{medicine}', [CartController::class, 'store'])->name('cart.store');
        Route::put('/cart/items/{cartItem}', [CartController::class, 'update'])->name('cart.update');
        Route::delete('/cart/items/{cartItem}', [CartController::class, 'destroy'])->name('cart.destroy');
        Route::get('/checkout', [CheckoutController::class, 'create'])->name('checkout.create');
        Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::post('/orders/{order}/rating', [RatingController::class, 'store'])->name('orders.rating.store');
        Route::get('/wallet', [WalletController::class, 'index'])->name('wallet.index');
        Route::get('/campaigns', [CampaignBrowseController::class, 'index'])->name('campaigns.index');
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::patch('/notifications/{notification}/read', [NotificationController::class, 'read'])->name('notifications.read');
        Route::get('/ai-chat', [AiChatController::class, 'index'])->name('ai-chat.index');
        Route::post('/ai-chat', [AiChatController::class, 'store'])->middleware('throttle:10,1')->name('ai-chat.store');
        Route::delete('/ai-chat', [AiChatController::class, 'clear'])->name('ai-chat.clear');
    });
    Route::middleware('role:rider')->group(function () {
        Route::get('/deliveries', [DeliveryController::class, 'index'])->name('delivery.index');
        Route::patch('/deliveries/{delivery}/status', [DeliveryController::class, 'updateStatus'])->name('delivery.status');
    });
    Route::middleware('role:admin,pharmacy_staff')->group(function () {
        Route::get('/manage/orders', [OrderController::class, 'index'])->name('manage.orders.index');
        Route::get('/manage/orders/{order}', [OrderController::class, 'show'])->name('manage.orders.show');
        Route::patch('/manage/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('manage.orders.status');
    });
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('/admin/reports', [AdminDashboardController::class, 'reports'])->name('admin.reports');
        Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users.index');
        Route::get('/admin/users/create', [AdminUserController::class, 'create'])->name('admin.users.create');
        Route::post('/admin/users', [AdminUserController::class, 'store'])->name('admin.users.store');
        Route::patch('/admin/users/{user}/toggle', [AdminUserController::class, 'toggle'])->name('admin.users.toggle');
        Route::patch('/admin/users/{user}/password', [AdminUserController::class, 'resetPassword'])->name('admin.users.password');
        Route::resource('/admin/campaigns', CampaignController::class)->except(['show'])->names('admin.campaigns');
        Route::get('/admin/wallets/{wallet}', [AdminWalletController::class, 'show'])->name('admin.wallets.show');
        Route::get('/admin/users/{user}/wallet', [AdminWalletController::class, 'customer'])->name('admin.users.wallet');
        Route::get('/admin/pharmacies/{pharmacy}/wallet', [AdminWalletController::class, 'pharmacy'])->name('admin.pharmacies.wallet');
        Route::post('/admin/wallets/{wallet}/adjust', [AdminWalletController::class, 'adjust'])->name('admin.wallets.adjust');
        Route::post('/admin/orders/{order}/assign-rider', [AdminDeliveryController::class, 'assign'])->name('admin.orders.assign-rider');
    });
});
