<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;

class SyncController extends Controller
{
    public function syncGoogle()
    {
        $user = Auth::user();

        Artisan::call('app:calendar-sync-command', [
            'user_id' => $user->id,
        ]);

        return back()->with('success', 'Google Calendar sync successful.');
    }

    public function syncTodo()
    {
        $user = Auth::user();

        Artisan::call('app:todo-sync-command', [
            'user_id' => $user->id,
        ]);

        return back()->with('success', 'Todo Sync successful.');

    }
}
