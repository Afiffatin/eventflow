<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MyTicketsController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Organizer\DashboardController as OrgDashboardController;
use App\Http\Controllers\Organizer\EventController as OrgEventController;
use App\Http\Controllers\Organizer\TicketTierController;
use App\Http\Controllers\Organizer\OrderController as OrgOrderController;
use App\Http\Controllers\Organizer\ScannerController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ReportController;
use Illuminate\Support\Facades\Route;

// ═══════════════════════════════════════════════════════════
// PUBLIC
// ═══════════════════════════════════════════════════════════

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{slug}', [EventController::class, 'show'])->name('events.show');

// ═══════════════════════════════════════════════════════════
// AUTHENTICATED (any role)
// ═══════════════════════════════════════════════════════════

Route::middleware('auth')->group(function () {

    // Customer dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Checkout flow
    Route::post('/checkout/{event}', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/{order}/summary', [CheckoutController::class, 'summary'])->name('checkout.summary');
    Route::post('/checkout/{order}/pay', [CheckoutController::class, 'pay'])->name('checkout.pay');
    Route::get('/checkout/{order}/success', [CheckoutController::class, 'success'])->name('checkout.success');
    
    // Mock Payment Webhook for PRK42-35
    Route::post('/payment/mock-webhook', [CheckoutController::class, 'mockWebhook'])->name('payment.webhook');

    // My tickets
    Route::get('/my-tickets', [MyTicketsController::class, 'index'])->name('tickets.index');
    Route::get('/my-tickets/{attendee}', [MyTicketsController::class, 'show'])->name('tickets.show');
    Route::get('/my-tickets/{attendee}/pdf', [MyTicketsController::class, 'downloadPdf'])->name('tickets.pdf');

    // Orders
    Route::get('/orders', [OrdersController::class, 'index'])->name('orders.index');
    Route::get('/orders/export/csv', [OrdersController::class, 'exportCsv'])->name('orders.export.csv');
    Route::get('/orders/{order}', [OrdersController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/cancel', [OrdersController::class, 'cancel'])->name('orders.cancel');

    // Profile (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ═══════════════════════════════════════════════════════════
// ORGANIZER
// ═══════════════════════════════════════════════════════════

Route::middleware(['auth', 'role:organizer,admin'])->prefix('organizer')->name('organizer.')->group(function () {

    Route::get('/dashboard', [OrgDashboardController::class, 'index'])->name('dashboard');

    // Event CRUD
    Route::resource('events', OrgEventController::class)->except('show');

    // Ticket Tiers (nested under event)
    Route::post('/events/{event}/tiers', [TicketTierController::class, 'store'])->name('tiers.store');
    Route::put('/events/{event}/tiers/{tier}', [TicketTierController::class, 'update'])->name('tiers.update');
    Route::delete('/events/{event}/tiers/{tier}', [TicketTierController::class, 'destroy'])->name('tiers.destroy');

    // Orders per event
    Route::get('/events/{event}/orders', [OrgOrderController::class, 'index'])->name('events.orders');
    Route::get('/events/{event}/orders/{order}', [OrgOrderController::class, 'show'])->name('events.orders.show');
    Route::get('/events/{event}/export/csv', [OrgOrderController::class, 'exportCsv'])->name('events.export.csv');

    // Scanner
    Route::get('/scanner', [ScannerController::class, 'index'])->name('scanner');
    Route::post('/scanner/check-in', [ScannerController::class, 'checkIn'])->name('scanner.checkin');
});

// ═══════════════════════════════════════════════════════════
// ADMIN
// ═══════════════════════════════════════════════════════════

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Users
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::patch('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // Categories
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::patch('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // Events
    Route::get('/events', [AdminEventController::class, 'index'])->name('events.index');
    Route::patch('/events/{event}', [AdminEventController::class, 'updateStatus'])->name('events.update');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports');
});

// ═══════════════════════════════════════════════════════════
// AUTH (Breeze)
// ═══════════════════════════════════════════════════════════

require __DIR__.'/auth.php';
