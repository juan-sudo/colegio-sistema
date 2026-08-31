<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = ['name', 'code', 'description'];

    public function gradeSections()
    {
        return $this->belongsToMany(GradeSection::class, 'grade_section_subject')
            ->withPivot('teacher_id', 'hours_per_week')
            ->withTimestamps();
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}
