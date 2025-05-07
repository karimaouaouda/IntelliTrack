<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Observers\UserObserver;
use App\Traits\Attendable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Traits\HasProfile;

#[ObservedBy(UserObserver::class)]
class User extends Authenticatable implements FilamentUser, \App\Interfaces\Attendable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, Attendable, HasProfile, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'ref_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->map(fn (string $name) => Str::of($name)->substr(0, 1))
            ->implode('');
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    public function modules(): BelongsToMany
    {
        return $this->belongsToMany(Module::class, 'module_teacher')
            ->withTimestamps();
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class, 'teacher_id');
    }

    public function classrooms(): BelongsToMany
    {
        return $this->belongsToMany(Classroom::class, 'teacher_classroom')
            ->withPivot(['subject', 'is_primary_teacher', 'schedule', 'notes'])
            ->withTimestamps();
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'parent_student')
            ->withPivot(['relationship_type', 'is_primary_contact', 'notes'])
            ->withTimestamps();
    }

    public function isTeacher(): bool
    {
        return $this->hasRole('teacher');
    }

    public function isParent(): bool
    {
        return $this->hasRole('parent');
    }

    public function getTeacherSchedules()
    {
        return $this->isTeacher() ? $this->schedules : collect();
    }

    public function getStudentChildren()
    {
        return $this->isParent() ? $this->students : collect();
    }

    public function getPrimaryClassrooms()
    {
        return $this->classrooms()
            ->wherePivot('is_primary_teacher', true)
            ->get();
    }

    public function profile(){
        return $this->hasOne(TeacherProfile::class, 'user_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (empty($user->ref_id)) {
                $user->ref_id = 'USR-' . strtoupper(Str::random(8));
            }
        });
    }

    public function toArray()
    {
        return [
          ...parent::toArray(),
          'role' => $this->roles()->first()?->name ?? 'parent'
        ];
    }
}
