<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasFullName;

class Guardian extends Model
{
    use HasFullName;

    protected $fillable = ["user_id", "first_name", "last_name", "phone_whatsapp"];

    public function user()     { return $this->belongsTo(User::class); }
    public function students() { return $this->belongsToMany(Student::class, "guardian_student"); }
}
