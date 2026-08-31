<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = [
        'grade_section_id', 'subject_id', 'teacher_id',
        'shift_id', 'day_of_week', 'start_time', 'end_time', 'classroom'
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    public function gradeSection()
    {
        return $this->belongsTo(GradeSection::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }
}
