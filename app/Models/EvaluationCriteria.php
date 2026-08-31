<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvaluationCriteria extends Model
{
    protected $table = 'evaluation_criteria';

    protected $fillable = ['name', 'description'];

    public function gradeSections()
    {
        return $this->belongsToMany(GradeSection::class, 'grade_eval_criteria')
            ->withTimestamps();
    }

    public function assessmentCriteria()
    {
        return $this->hasMany(AssessmentCriterion::class);
    }

    public function criterionGrades()
    {
        return $this->hasMany(CriterionGrade::class);
    }
}
