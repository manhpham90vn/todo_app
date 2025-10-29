<?php

use App\Http\Controllers\GoogleOAuthController;
use App\Http\Controllers\SyncController;
use App\Http\Controllers\TodoController;
use Illuminate\Support\Facades\Route;

Auth::routes(['verify' => true]);

Route::get('/', function () {
    return redirect()->route('todos.index');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('todos', TodoController::class);
    Route::get('todos-trash', [TodoController::class, 'trash'])->name('todos.trash');
    Route::put('todos/{id}/restore', [TodoController::class, 'restore'])->name('todos.restore');
    Route::delete('todos/{id}/force', [TodoController::class, 'forceDelete'])->name('todos.forceDelete');
    Route::patch('todos/{todo}/toggle-complete', [TodoController::class, 'toggleComplete'])->name('todos.toggle');
    Route::get('/google/login', [GoogleOAuthController::class, 'redirect'])->name('google.oauth.redirect');
    Route::get('/google/oauth/callback', [GoogleOAuthController::class, 'callback'])->name('google.oauth.callback');
    Route::post('/sync-google-calendar', [SyncController::class, 'syncGoogle'])->middleware('throttle:4,1')->name('google.calendar.sync');
    Route::post('/sync-todo', [SyncController::class, 'syncTodo'])->middleware('throttle:4,1')->name('todo.sync');
});
