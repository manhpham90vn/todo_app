<?php

namespace App\Notifications;

use App\Models\Todo;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class BrowserPush extends Notification
{
    use Queueable;

    public function __construct(public Todo $todo) {}

    public function via(object $notifiable): array
    {
        return [WebPushChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toWebPush(object $notifiable)
    {
        $body = $this->todo->description;
        if (! empty($this->todo->start_at)) {
            $body .= ' '.$this->todo->start_at->toDateTimeString();
        }

        return (new WebPushMessage)
            ->title($this->todo->title)
            ->body($body)
            ->action('Xem ngay', 'open_app');
    }
}
