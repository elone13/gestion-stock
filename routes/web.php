<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\ProfileController;
Route::get('/', function () {
    return view('welcome');
});

// Inscription
Route::get('/register', [RegisteredUserController::class, 'create'])
    ->middleware('guest')
    ->name('register');

Route::post('/register', [RegisteredUserController::class, 'store'])
    ->middleware('guest');

// Connexion
Route::get('/login', [AuthenticatedSessionController::class, 'create'])
    ->middleware('guest')
    ->name('login');

Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware('guest');

// Déconnexion
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// Réinitialisation du mot de passe - Demande du lien
Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])
    ->middleware('guest')
    ->name('password.request');

Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
    ->middleware('guest')
    ->name('password.email');

// Réinitialisation du mot de passe - Formulaire de reset
Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])
    ->middleware('guest')
    ->name('password.reset');

Route::post('/reset-password', [NewPasswordController::class, 'store'])
    ->middleware('guest')
    ->name('password.update');

// Vérification de l’email (notice)
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

// Vérification de l’email (lien cliqué)
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/dashboard');
})->middleware(['auth', 'signed'])->name('verification.verify');

// Renvoyer le mail de vérification
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Lien de vérification renvoyé !');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// Dashboard protégé par auth et vérification d’email
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');




Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Routes pour la gestion des produits
    Route::resource('products', \App\Http\Controllers\ProductController::class);
    
    // Routes pour la gestion des catégories
    Route::resource('categories', \App\Http\Controllers\CategoryController::class);
    
    // Routes pour la gestion des mouvements de stock
    Route::resource('stock-movements', \App\Http\Controllers\StockMovementController::class)->except(['edit', 'update', 'destroy']);
    Route::get('stock-movements/product/{product}/history', [\App\Http\Controllers\StockMovementController::class, 'productHistory'])->name('stock-movements.product-history');
    Route::get('stock-movements/quick-entry', [\App\Http\Controllers\StockMovementController::class, 'quickEntry'])->name('stock-movements.quick-entry');
    Route::get('stock-movements/quick-exit', [\App\Http\Controllers\StockMovementController::class, 'quickExit'])->name('stock-movements.quick-exit');
    
    // Routes pour les notifications
    Route::get('notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('notifications/{notification}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
    Route::patch('notifications/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::delete('notifications/{notification}', [\App\Http\Controllers\NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::get('notifications/unread-count', [\App\Http\Controllers\NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');
    Route::get('notifications/unread-list', [\App\Http\Controllers\NotificationController::class, 'getUnreadNotifications'])->name('notifications.unread-list');
    
    // Routes pour les rapports et exports
    Route::get('reports', [\App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/export-products', [\App\Http\Controllers\ReportController::class, 'exportProducts'])->name('reports.export-products');
    Route::get('reports/export-stock-movements', [\App\Http\Controllers\ReportController::class, 'exportStockMovements'])->name('reports.export-stock-movements');
    Route::get('reports/stock-value', [\App\Http\Controllers\ReportController::class, 'stockValue'])->name('reports.stock-value');
    Route::get('reports/category', [\App\Http\Controllers\ReportController::class, 'categoryReport'])->name('reports.category');
    Route::get('reports/movements', [\App\Http\Controllers\ReportController::class, 'movementReport'])->name('reports.movements');
});
