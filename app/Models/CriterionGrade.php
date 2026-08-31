<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CriterionGrade extends Model
{
    protected $fillable = ['assessment_criterion_id', 'student_id', 'score', 'recorded_by'];

    public function criterion()
    {
        return $this->belongsTo(AssessmentCriterion::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
