<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'name_en',
        'name_sr',
        'location',
        'capacity',
        'description_en',
        'description_sr',
        'photo_path',
        'color_hex',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'capacity' => 'integer',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function resolveRouteBinding($value, $field = null): ?Model
    {
        return $this->where('slug', $value)
            ->orWhere('id', is_numeric($value) ? (int) $value : 0)
            ->first();
    }

    public function equipments(): HasMany
    {
        return $this->hasMany(RoomEquipment::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function localizedName(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();

        return $locale === 'sr_Latn' ? ($this->name_sr ?: $this->name_en) : ($this->name_en ?: $this->name_sr);
    }

    public function localizedDescription(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();

        return $locale === 'sr_Latn'
            ? ($this->description_sr ?: $this->description_en ?: '')
            : ($this->description_en ?: $this->description_sr ?: '');
    }

    public function photoUrl(): ?string
    {
        if ($this->photo_path && Storage::disk('public')->exists($this->photo_path)) {
            return Storage::disk('public')->url($this->photo_path);
        }

        return null;
    }
}
