<?php

namespace App\Listeners;

use App\Mail\WelcomeEmail;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Mail;

class WelcomeListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Verified $event): void
    {
        $user = $event->user;
        Mail::to($user->getEmailForVerification())->send(new WelcomeEmail($user));
    }
}
