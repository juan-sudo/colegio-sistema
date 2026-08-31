<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradePeriod extends Model
{
    protected $fillable = ["name", "school_year", "start_date", "end_date", "school_phase_id"];

    public function schoolPhase() { return $this->belongsTo(SchoolPhase::class); }
    public function grades() { return $this->hasMany(Grade::class); }
}
