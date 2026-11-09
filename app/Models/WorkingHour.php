<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkingHour extends Model
{
    use HasFactory;

    protected $table = 'working_hours';

    protected $fillable = [
        'day_of_week',
        'is_open',
        'open_time',
        'close_time',
    ];

    protected function casts(): array
    {
        return [
            'day_of_week' => 'integer',
            'is_open' => 'boolean',
        ];
    }

    public static function dayNames(): array
    {
        return [
            1 => ['en' => 'Monday', 'sr_Latn' => 'Ponedeljak'],
            2 => ['en' => 'Tuesday', 'sr_Latn' => 'Utorak'],
            3 => ['en' => 'Wednesday', 'sr_Latn' => 'Sreda'],
            4 => ['en' => 'Thursday', 'sr_Latn' => 'Četvrtak'],
            5 => ['en' => 'Friday', 'sr_Latn' => 'Petak'],
            6 => ['en' => 'Saturday', 'sr_Latn' => 'Subota'],
            7 => ['en' => 'Sunday', 'sr_Latn' => 'Nedelja'],
        ];
    }

    public function localizedDayName(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $days = self::dayNames();

        return $days[$this->day_of_week][$locale] ?? $days[$this->day_of_week]['en'] ?? 'Day ' . $this->day_of_week;
    }
}
