<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'title_en',
        'title_sr',
        'event_date',
        'start_time',
        'end_time',
        'location',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
        ];
    }

    public function localizedTitle(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();

        return $locale === 'sr_Latn' ? ($this->title_sr ?: $this->title_en) : ($this->title_en ?: $this->title_sr);
    }
}
