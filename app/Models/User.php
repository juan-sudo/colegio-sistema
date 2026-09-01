<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Traits\HasRole;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRole;

    protected $fillable = ["name", "email", "password", "role", "phone", "active"];
    protected $hidden = ["password", "remember_token"];
    protected $casts = ["email_verified_at" => "datetime", "active" => "boolean"];

    public function student()  { return $this->hasOne(Student::class); }
    public function teacher()  { return $this->hasOne(Teacher::class); }
    public function guardian() { return $this->hasOne(Guardian::class); }
}
