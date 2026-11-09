<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_code',
        'management_token',
        'room_id',
        'created_by_user_id',
        'booking_date',
        'start_at',
        'end_at',
        'duration_minutes',
        'title',
        'agenda',
        'organizer_name',
        'organizer_email',
        'attendees_count',
        'status',
        'locale',
        'cancellation_reason',
        'cancelled_at',
        'cancelled_by',
    ];

    protected function casts(): array
    {
        return [
            'booking_date' => 'date',
            'start_at' => 'datetime',
            'end_at' => 'datetime',
            'duration_minutes' => 'integer',
            'attendees_count' => 'integer',
            'cancelled_at' => 'datetime',
        ];
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(BookingStatusHistory::class);
    }

    public function simulatedEmails(): HasMany
    {
        return $this->hasMany(SimulatedEmail::class);
    }

    public function scopeConfirmed(Builder $query): Builder
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeCancelled(Builder $query): Builder
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeForDate(Builder $query, $date): Builder
    {
        return $query->whereDate('booking_date', $date);
    }

    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function canBeManaged(?string $timezone = null): bool
    {
        if (! $this->isConfirmed()) {
            return false;
        }

        $tz = $timezone ?? config('app.timezone', 'Europe/Belgrade');
        $now = Carbon::now($tz);
        $start = $this->start_at->copy()->timezone($tz);

        return $now->lessThan($start);
    }

    public function timeRangeString(): string
    {
        return $this->start_at->format('H:i') . ' – ' . $this->end_at->format('H:i');
    }
}
