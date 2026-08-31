<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class EvaluationPeriod extends Model { protected $fillable=['school_year_id','name','code','weight','starts_at','ends_at','closed']; protected $casts=['starts_at'=>'date','ends_at'=>'date','closed'=>'boolean']; public function schoolYear(){return $this->belongsTo(SchoolYear::class);} public function criteria(){return $this->hasMany(AssessmentCriterion::class);} }
