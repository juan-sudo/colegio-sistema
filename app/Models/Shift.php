<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    protected $fillable = ['name', 'start_time', 'end_time'];

    protected $casts = [
        'start_time' => 'time',
        'end_time' => 'time',
    ];

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function getStartTimeAttribute($value): string
    {
        return \Carbon\Carbon::createFromFormat('H:i:s', $value)->format('H:i');
    }

    public function getEndTimeAttribute($value): string
    {
        return \Carbon\Carbon::createFromFormat('H:i:s', $value)->format('H:i');
    }
}
