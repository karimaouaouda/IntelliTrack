<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendable_id',
        'attendable_type',
        'type',
        'recorded_at',
        'device_id',
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
    ];

    public function attendable(): MorphTo
    {
        return $this->morphTo();
    }
} 