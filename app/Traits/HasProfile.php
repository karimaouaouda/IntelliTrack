<?php

namespace App\Traits;

use App\Models\TeacherProfile;
use Illuminate\Database\Eloquent\Relations\HasOne;

trait HasProfile
{
    public function teacherProfile(): HasOne
    {
        return $this->hasOne(TeacherProfile::class);
    }

    public function getTeacherProfile(): ?TeacherProfile
    {
        return $this->isTeacher() ? $this->teacherProfile : null;
    }

    public function hasTeacherProfile(): bool
    {
        return $this->isTeacher() && $this->teacherProfile()->exists();
    }

    public function createTeacherProfile(array $attributes = []): TeacherProfile
    {
        if (!$this->isTeacher()) {
            throw new \RuntimeException('Only teachers can have teacher profiles.');
        }

        if ($this->hasTeacherProfile()) {
            throw new \RuntimeException('This teacher already has a profile.');
        }

        return $this->teacherProfile()->create($attributes);
    }

    public function updateTeacherProfile(array $attributes = []): bool
    {
        if (!$this->hasTeacherProfile()) {
            return false;
        }

        return $this->teacherProfile()->update($attributes);
    }

    public function deleteTeacherProfile(): bool
    {
        if (!$this->hasTeacherProfile()) {
            return false;
        }

        return $this->teacherProfile()->delete();
    }

    public function getProfilePhotoUrl(): ?string
    {
        if ($this->isTeacher() && $this->hasTeacherProfile()) {
            return $this->teacherProfile->profile_photo;
        }

        return null;
    }

    public function getFullName(): string
    {
        return $this->name;
    }

    public function getDisplayName(): string
    {
        if ($this->isTeacher() && $this->hasTeacherProfile()) {
            return "{$this->name} ({$this->teacherProfile->subject})";
        }

        return $this->name;
    }

    public function getContactInfo(): array
    {
        $info = [
            'email' => $this->email,
        ];

        if ($this->isTeacher() && $this->hasTeacherProfile()) {
            $info = array_merge($info, [
                'phone' => $this->teacherProfile->phone,
                'address' => $this->teacherProfile->address,
                'emergency_contact' => $this->teacherProfile->emergency_contact,
            ]);
        }

        return $info;
    }

    public function getProfessionalInfo(): array
    {
        if (!$this->isTeacher() || !$this->hasTeacherProfile()) {
            return [];
        }

        return [
            'subject' => $this->teacherProfile->subject,
            'qualification' => $this->teacherProfile->qualification,
            'experience_years' => $this->teacherProfile->experience_years,
            'specialization' => $this->teacherProfile->specialization,
            'certifications' => $this->teacherProfile->certifications,
            'skills' => $this->teacherProfile->skills,
            'employment_status' => $this->teacherProfile->employment_status,
            'joining_date' => $this->teacherProfile->joining_date,
            'contract_end_date' => $this->teacherProfile->contract_end_date,
        ];
    }
} 