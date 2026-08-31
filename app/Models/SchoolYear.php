<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SchoolYear extends Model { protected $fillable=['name','starts_at','ends_at','status']; protected $casts=['starts_at'=>'date','ends_at'=>'date']; public function enrollments(){return $this->hasMany(Enrollment::class);} public function periods(){return $this->hasMany(EvaluationPeriod::class);} }
