<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subject',
        'qualification',
        'experience_years',
        'specialization',
        'bio',
        'phone',
        'address',
        'emergency_contact',
        'certifications',
        'skills',
        'profile_photo',
        'joining_date',
        'contract_end_date',
        'employment_status',
        'salary',
        'working_hours',
        'notes',
    ];

    protected $casts = [
        'certifications' => 'array',
        'skills' => 'array',
        'working_hours' => 'array',
        'joining_date' => 'date',
        'contract_end_date' => 'date',
        'salary' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isActive(): bool
    {
        return $this->employment_status === 'active';
    }

    public function isOnLeave(): bool
    {
        return $this->employment_status === 'on_leave';
    }

    public function isTerminated(): bool
    {
        return $this->employment_status === 'terminated';
    }

    public function hasContract(): bool
    {
        return !empty($this->contract_end_date);
    }

    public function isContractExpired(): bool
    {
        return $this->hasContract() && $this->contract_end_date->isPast();
    }

    public function getWorkingHoursForDay(string $day): ?array
    {
        return $this->working_hours[$day] ?? null;
    }

    public function getFullQualification(): string
    {
        return "{$this->qualification} ({$this->experience_years} years experience)";
    }

    public function getSpecializationsList(): array
    {
        return array_filter([
            $this->specialization,
            ...($this->skills ?? []),
        ]);
    }
} 