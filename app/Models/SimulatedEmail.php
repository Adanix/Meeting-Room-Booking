<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SimulatedEmail extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'event_type',
        'recipient_type',
        'recipient_email',
        'recipient_name',
        'subject',
        'locale',
        'body_html',
        'body_text',
        'manage_url',
        'ics_url',
        'google_calendar_url',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
