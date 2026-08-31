<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasFullName;

class Teacher extends Model
{
    use HasFullName;

    protected $fillable = ["user_id", "code", "first_name", "last_name", "specialty"];

    public function user()    { return $this->belongsTo(User::class); }
    public function courses() { return $this->hasMany(Course::class); }
}
