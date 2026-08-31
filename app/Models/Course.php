<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = ["name", "grade_section_id", "teacher_id", "school_year", "academic_year_id", "subject_id"];

    public function academicYear() { return $this->belongsTo(AcademicYear::class); }
    public function gradeSection() { return $this->belongsTo(GradeSection::class); }
    public function teacher()      { return $this->belongsTo(Teacher::class); }
    public function subject()      { return $this->belongsTo(Subject::class); }
    public function students()     { return $this->belongsToMany(Student::class, "course_student"); }
    public function assignments()  { return $this->hasMany(Assignment::class); }
    public function grades()       { return $this->hasMany(Grade::class); }
    public function attendances()  { return $this->hasMany(Attendance::class); }
    public function schedules()    { return $this->hasMany(Schedule::class); }
    public function assessmentCriteria() { return $this->hasMany(AssessmentCriterion::class); }
}
