<?php

namespace App\Models\Traits;

trait HasFullName
{
    public function fullName(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
