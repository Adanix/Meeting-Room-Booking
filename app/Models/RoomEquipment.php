<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomEquipment extends Model
{
    use HasFactory;

    protected $table = 'room_equipments';

    protected $fillable = [
        'room_id',
        'code',
        'label_en',
        'label_sr',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function localizedLabel(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();

        return $locale === 'sr_Latn' ? ($this->label_sr ?: $this->label_en) : ($this->label_en ?: $this->label_sr);
    }
}
