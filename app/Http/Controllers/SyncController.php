<?php

namespace App\Http\Controllers;

use App\Jobs\SyncGoogleCalendarJob;
use App\Jobs\SyncTodoJob;
use Illuminate\Support\Facades\Auth;

class SyncController extends Controller
{
    public function syncGoogle()
    {
        $user = Auth::user();
        $token = $user->googleTokens()->latest('created_at')->first();

        dispatch(new SyncGoogleCalendarJob($user->id, $token));

        return back()->with('success', 'Google Calendar sync successful.');
    }

    public function syncTodo()
    {
        $user = Auth::user();
        $event = $user->events();

        dispatch(new SyncTodoJob($user->id, $event));

        return back()->with('success', 'Todo Sync successful.');

    }
}
