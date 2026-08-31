<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasFullName;

class Student extends Model
{
    use HasFullName;

    protected $fillable = [
        "user_id", "grade_section_id", "dni", "code", "qr_token", "barcode",
        "first_name", "last_name", "birth_date", "photo", "biometric_id", "active",
    ];
    protected $casts = ["birth_date" => "date", "active" => "boolean"];

    public function user()         { return $this->belongsTo(User::class); }
    public function gradeSection() { return $this->belongsTo(GradeSection::class); }
    public function courses()      { return $this->belongsToMany(Course::class, "course_student"); }
    public function attendances()  { return $this->hasMany(Attendance::class); }
    public function grades()       { return $this->hasMany(Grade::class); }
    public function submissions()  { return $this->hasMany(Submission::class); }
    public function guardians()    { return $this->belongsToMany(Guardian::class, "guardian_student"); }
}
