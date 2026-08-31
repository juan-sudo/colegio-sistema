<?php

namespace App\Models\Traits;

trait HasRole
{
    public function isAdmin(): bool
    {
        return $this->role === "admin";
    }

    public function isTeacher(): bool
    {
        return $this->role === "teacher";
    }

    public function isParent(): bool
    {
        return $this->role === "parent";
    }

    public function isStudent(): bool
    {
        return $this->role === "student";
    }
}
