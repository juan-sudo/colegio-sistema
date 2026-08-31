<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    protected $fillable = [
        "assignment_id", "student_id", "file_path", "submitted_at",
        "grade", "feedback", "status",
    ];
    protected $casts = ["submitted_at" => "datetime"];

    public function assignment() { return $this->belongsTo(Assignment::class); }
    public function student()    { return $this->belongsTo(Student::class); }
}
