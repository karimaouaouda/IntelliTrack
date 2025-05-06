<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Module extends Model
{
    /** @use HasFactory<\Database\Factories\ModuleFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'credits',
        'code',
    ];

    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'module_teacher')
            ->whereHas('roles', function ($query) {
                $query->where('name', 'teacher');
            })
            ->withTimestamps();
    }

    public function classrooms(): BelongsToMany
    {
        return $this->belongsToMany(Classroom::class, 'module_classroom')
            ->withTimestamps();
    }
}
