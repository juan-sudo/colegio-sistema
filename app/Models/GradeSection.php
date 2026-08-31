<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradeSection extends Model
{
    protected $fillable = ["name", "level", "school_year", "academic_year_id", "shift_id"];

    public function academicYear() { return $this->belongsTo(AcademicYear::class); }
    public function shift()        { return $this->belongsTo(Shift::class); }
    public function students()     { return $this->hasMany(Student::class); }
    public function courses()      { return $this->hasMany(Course::class); }
    public function schedules()    { return $this->hasMany(Schedule::class); }
    public function subjects()     { return $this->belongsToMany(Subject::class, "grade_section_subject"); }
}
