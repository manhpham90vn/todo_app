<?php

use App\Http\Controllers\TodoController;
use App\Models\GoogleToken;
use Google\Client;
use Google\Service\Calendar;
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
    Route::get('/google/login', function () {
        $client = new Client;
        $client->setClientId(config('google.google_calendar_client_id'));
        $client->setClientSecret(config('google.google_calendar_client_secret'));
        $client->setRedirectUri(config('google.google_calendar_redirect_uri'));
        $client->setScopes([Calendar::CALENDAR_READONLY, 'openid', 'email']);
        $client->setAccessType('offline');
        $client->setPrompt('consent');

        return redirect()->away($client->createAuthUrl());
    })->name('google.oauth.redirect');

    Route::get('/google/oauth/callback', function () {
        if (! request('code')) {
            abort(400, 'Missing code');
        }

        $client = new Client;
        $client->setClientId(config('google.google_calendar_client_id'));
        $client->setClientSecret(config('google.google_calendar_client_secret'));
        $client->setRedirectUri(config('google.google_calendar_redirect_uri'));

        $token = $client->fetchAccessTokenWithAuthCode(request('code'));
        if (isset($token['error'])) {
            abort(400, $token['error_description'] ?? 'OAuth error');
        }

        GoogleToken::create($token + ['user_id' => auth()->id()]);

        return redirect()->route('todos.index')->with('success', 'Google OAuth successful. Tokens have been stored.');
    })->name('google.oauth.callback');
    Route::get('/sync-google-calendar', function () {
        Artisan::call('app:calendar-sync-command');

        return back()->with('success', 'Google Calendar sync successful.');
    })->name('google.calendar.sync');
    Route::get('/sync-todo', function () {
        Artisan::call('app:todo-sync-command');

        return back()->with('success', 'Todo Sync successful.');
    })->name('todo.sync');
});
