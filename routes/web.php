<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CloudflareController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Inertia\Inertia;

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::prefix('cloudflare')->group(function () {
    Route::post('/{zoneId}/ssl-mode', [CloudflareController::class, 'updateSSLMode']);
    Route::post('/{zoneId}/page-rule', [CloudflareController::class, 'createPageRule']);
    Route::delete('/{zoneId}/page-rule/{ruleId}', [CloudflareController::class, 'deletePageRule']);
    Route::get('/zones', [CloudflareController::class, 'getZoneId']);
    Route::get('/{domainId}/page-rules', [CloudflareController::class, 'getPageRules']);

    Route::get('/get-accounts/', [CloudflareController::class, 'index']);
    Route::post('/accounts', [CloudflareController::class, 'addAccount']);

    Route::get('/accounts/{account}', [CloudflareController::class, 'showAccount']);

});
Route::get('/domains', [CloudflareController::class, 'getDomains']);
Route::post('/domains', [CloudflareController::class, 'addDomain']);
Route::post('/domain/delete', [CloudflareController::class, 'deleteDomain']);

Route::get('/cloudflare/accounts', function () {
    return Inertia::render('CloudflareAccounts');
})->name('cloudflare');

Route::get('/domains/{id}/settings/{account_id}', function ($id, $account_id) {
    return Inertia::render('DomainSettings', ['domainId' => $id, 'account_id' => $account_id]);
})->name('domains.settings');

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

require __DIR__.'/auth.php';

