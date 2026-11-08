<?php

use App\Http\Controllers\ClientAccessController;
use App\Http\Controllers\ClientContactController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GeminiAssistantController;
use App\Http\Controllers\OnboardingChecklistController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QaReviewController;
use App\Http\Controllers\TicketAttachmentController;
use App\Http\Controllers\TicketCommentController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // CRM Clientes
    Route::resource('clients', ClientController::class);

    // Contactos de Cliente
    Route::post('/clients/{client}/contacts', [ClientContactController::class, 'store'])->name('clients.contacts.store');
    Route::delete('/clients/{client}/contacts/{contact}', [ClientContactController::class, 'destroy'])->name('clients.contacts.destroy');

    // Bóveda de Accesos
    Route::post('/clients/{client}/accesses', [ClientAccessController::class, 'store'])->name('clients.accesses.store');
    Route::delete('/clients/{client}/accesses/{access?}', [ClientAccessController::class, 'destroy'])->name('clients.accesses.destroy');

    // Onboarding Checklist
    Route::post('/clients/{client}/onboarding/items/{item}/toggle', [OnboardingChecklistController::class, 'toggle'])->name('clients.onboarding.toggle');
    Route::post('/clients/{client}/onboarding/items', [OnboardingChecklistController::class, 'storeItem'])->name('clients.onboarding.items.store');

    // Tickets Kanban
    Route::resource('tickets', TicketController::class);
    Route::post('/tickets/{ticket}/status', [TicketController::class, 'updateStatus'])->name('tickets.update-status');

    // Control de Calidad QA
    Route::post('/tickets/{ticket}/qa-reviews', [QaReviewController::class, 'store'])->name('tickets.qa-reviews.store');

    // Comentarios y Discusión de Tickets
    Route::post('/tickets/{ticket}/comments', [TicketCommentController::class, 'store'])->name('tickets.comments.store');

    // Archivos Adjuntos
    Route::post('/tickets/{ticket}/attachments', [TicketAttachmentController::class, 'store'])->name('tickets.attachments.store');
    Route::delete('/tickets/{ticket}/attachments/{attachment}', [TicketAttachmentController::class, 'destroy'])->name('tickets.attachments.destroy');

    // Asistente Gemini IA
    Route::post('/tickets/{ticket}/gemini-analyze', [GeminiAssistantController::class, 'analyze'])->name('tickets.gemini-analyze');

    // Gestión Unificada de Empleados, Roles y Permisos (Administradores y Super Admin)
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', [\App\Http\Controllers\AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [\App\Http\Controllers\AdminUserController::class, 'create'])->name('users.create');
        Route::post('/users', [\App\Http\Controllers\AdminUserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [\App\Http\Controllers\AdminUserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [\App\Http\Controllers\AdminUserController::class, 'update'])->name('users.update');
        Route::post('/users/{user}/toggle', [\App\Http\Controllers\AdminUserController::class, 'toggleStatus'])->name('users.toggle');
        Route::delete('/users/{user}', [\App\Http\Controllers\AdminUserController::class, 'destroy'])->name('users.destroy');
    });
});

require __DIR__.'/auth.php';
