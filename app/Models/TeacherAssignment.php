<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class TeacherAssignment extends Model { protected $fillable=['school_year_id','teacher_id','course_id','grade_section_id','weekly_hours']; public function teacher(){return $this->belongsTo(Teacher::class);} public function course(){return $this->belongsTo(Course::class);} public function section(){return $this->belongsTo(GradeSection::class,'grade_section_id');} public function schedules(){return $this->hasMany(Schedule::class);} }
