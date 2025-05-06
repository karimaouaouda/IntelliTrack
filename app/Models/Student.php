<?php

namespace App\Models;

use App\Traits\Attendable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Student extends Model implements \App\Interfaces\Attendable
{
    /** @use HasFactory<\Database\Factories\StudentFactory> */
    use HasFactory, Attendable;

    protected $fillable = [
        'name',
        'email',
        'ref_id',
        'date_of_birth',
        'gender',
        'address',
        'phone',
        'emergency_contact',
        'medical_conditions',
        'notes',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'medical_conditions' => 'array',
    ];

    public function classrooms(): BelongsToMany
    {
        return $this->belongsToMany(Classroom::class)
            ->withTimestamps();
    }

    public function parents(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'parent_student')
            ->withPivot(['relationship_type', 'is_primary_contact', 'notes'])
            ->withTimestamps();
    }

    public function getPrimaryContact()
    {
        return $this->parents()
            ->wherePivot('is_primary_contact', true)
            ->first();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($student) {
            if (empty($student->ref_id)) {
                $student->ref_id = 'STD-' . strtoupper(Str::random(8));
            }
        });
    }
}
