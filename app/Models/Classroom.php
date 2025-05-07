<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Classroom extends Model
{
    /** @use HasFactory<\Database\Factories\ClassroomFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'capacity',
        'description',
    ];

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class)
            ->withTimestamps();
    }

    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'teacher_classroom')
            ->withPivot(['subject', 'is_primary_teacher', 'schedule', 'notes'])
            ->withTimestamps();
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function getPrimaryTeacher()
    {
        return $this->teachers()
            ->wherePivot('is_primary_teacher', true)
            ->first();
    }

    public function getSubjectTeachers(string $subject)
    {
        return $this->teachers()
            ->wherePivot('subject', $subject)
            ->get();
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /**
     * @throws \Throwable
     */
    public function toArray()
    {
        return [
            ...parent::toArray(),
            'students' => $this->students()->get()->toResourceCollection()
        ];
    }
}
