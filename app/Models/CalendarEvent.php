<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CalendarEvent extends Model
{
    protected $fillable = [
        'user_id',
        'calendar_id',
        'google_event_id',
        'status',
        'summary',
        'description',
        'location',
        'start_at',
        'end_at',
        'attendees',
        'raw',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
