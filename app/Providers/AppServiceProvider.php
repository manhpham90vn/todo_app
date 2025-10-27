<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        DB::listen(function ($query) {
            logger()->info('Query Time: '.$query->time.'ms ['.$query->sql.']');
        });

        $watchedEvents = [
            \Illuminate\Auth\Events\Registered::class,
            \Illuminate\Auth\Events\PasswordReset::class,
            \Illuminate\Auth\Events\Verified::class,
        ];

        foreach ($watchedEvents as $eventClass) {
            Event::listen($eventClass, function ($event) use ($eventClass) {
                $userId = null;
                if (property_exists($event, 'user')) {
                    $user = $event->user;
                    if ($user instanceof \Illuminate\Contracts\Auth\Authenticatable ||
                        $user instanceof \Illuminate\Database\Eloquent\Model) {
                        $userId = $user->getKey();
                    } elseif (is_array($user) && isset($user['id'])) {
                        $userId = $user['id'];
                    }
                }

                logger()->info("Event fired: {$eventClass}", [
                    'payload_type' => get_class($event),
                    'user_id' => $userId,
                ]);
            });
        }
    }
}
