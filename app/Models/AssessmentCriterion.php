<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentCriterion extends Model
{
    protected $fillable = ['course_id', 'evaluation_criteria_id', 'name', 'description', 'maximum_score'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function evaluationCriteria()
    {
        return $this->belongsTo(EvaluationCriteria::class);
    }

    public function criterionGrades()
    {
        return $this->hasMany(CriterionGrade::class);
    }
}
