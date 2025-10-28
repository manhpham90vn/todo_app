<?php

use App\Http\Controllers\TodoController;
use Illuminate\Support\Facades\Route;

Auth::routes(['verify' => true]);

Route::get('/', function () {
    return redirect()->route('todos.index');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('todos', TodoController::class);

    Route::get('todos-trash', [TodoController::class,'trash'])->name('todos.trash');
    Route::put('todos/{id}/restore', [TodoController::class,'restore'])->name('todos.restore');
    Route::delete('todos/{id}/force', [TodoController::class,'forceDelete'])->name('todos.forceDelete');
    Route::patch('todos/{todo}/toggle-complete', [TodoController::class,'toggleComplete'])->name('todos.toggle');
});
