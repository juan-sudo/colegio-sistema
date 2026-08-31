<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        "student_id", "course_id", "date", "time_in", "status",
        "method", "recorded_by", "guardian_notified", "observation",
    ];
    protected $casts = ["date" => "date", "guardian_notified" => "boolean"];

    public function student() { return $this->belongsTo(Student::class); }
    public function course()  { return $this->belongsTo(Course::class); }
    public function recorder(){ return $this->belongsTo(User::class, "recorded_by"); }
}
