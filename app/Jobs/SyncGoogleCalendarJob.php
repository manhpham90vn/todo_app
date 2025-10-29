<?php

namespace App\Jobs;

use App\Models\CalendarEvent;
use App\Models\GoogleToken;
use App\Services\GoogleCalendarClient;
use Google\Service\Calendar\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class SyncGoogleCalendarJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $calendarId;

    public GoogleToken $token;

    public int $user_id;

    public function __construct(string $calendarId, GoogleToken $token, int $user_id)
    {
        $this->calendarId = $calendarId;
        $this->token = $token;
        $this->user_id = $user_id;
    }

    public function handle(): void
    {
        $service = GoogleCalendarClient::make($this->token);

        Log::info("Starting sync for calendar ID: {$this->calendarId}");

        $now = Carbon::now();
        $timeMin = $now->copy()->subDays(7)->startOfDay()->toRfc3339String();
        $timeMax = $now->copy()->addDays(7)->endOfDay()->addSecond()->toRfc3339String();

        $params = [
            'singleEvents' => true,
            'orderBy' => 'startTime',
            'timeMin' => $timeMin,
            'timeMax' => $timeMax,
            'maxResults' => 2500,
        ];

        $pageToken = null;

        do {
            if ($pageToken) {
                $params['pageToken'] = $pageToken;
            }

            $events = $service->events->listEvents($this->calendarId, $params);

            foreach ($events->getItems() as $event) {
                $this->upsertEvent($event, $this->user_id);
            }

            $pageToken = $events->getNextPageToken();
        } while ($pageToken);
    }

    protected function upsertEvent(Event $e, int $user_id): void
    {
        $start = $e->getStart()->getDateTime() ?: $e->getStart()->getDate();
        $end = $e->getEnd()->getDateTime() ?: $e->getEnd()->getDate();

        CalendarEvent::updateOrCreate(
            ['calendar_id' => $this->calendarId, 'google_event_id' => $e->getId(), 'user_id' => $user_id],
            [
                'status' => $e->getStatus(),
                'summary' => $e->getSummary(),
                'description' => $e->getDescription(),
                'location' => $e->getLocation(),
                'start_at' => $start ? Carbon::parse($start) : null,
                'end_at' => $end ? Carbon::parse($end) : null,
                'attendees' => $e->getAttendees() ? json_encode($e->getAttendees()) : null,
                'raw' => json_encode($e),
            ]
        );
    }
}
