<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolPhase extends Model
{
    protected $table = 'school_phases';

    protected $fillable = ['name', 'order'];

    public function gradePeriods()
    {
        return $this->hasMany(GradePeriod::class);
    }
}
