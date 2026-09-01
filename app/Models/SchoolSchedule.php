<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class SchoolSchedule extends Model
{
    protected $fillable = [
        'name',
        'entry_window_start',
        'entry_start',
        'late_until',
        'exit_time',
        'active',
    ];

    protected $casts = [
        'entry_window_start' => 'datetime:H:i',
        'entry_start' => 'datetime:H:i',
        'late_until' => 'datetime:H:i',
        'exit_time' => 'datetime:H:i',
        'active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public static function current(): ?self
    {
        return static::active()->orderBy('id')->first();
    }

    public function classify(Carbon $now): string
    {
        $windowStart = Carbon::parse($this->entry_window_start)->setDateFrom($now);
        $entryStart = Carbon::parse($this->entry_start)->setDateFrom($now);
        $lateUntil = Carbon::parse($this->late_until)->setDateFrom($now);

        if ($now->lt($windowStart)) {
            return 'presente';
        }
        if ($now->lt($entryStart)) {
            return 'presente';
        }
        if ($now->lte($lateUntil)) {
            return 'tardanza';
        }
        return 'falta';
    }

    public function toHumanArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'entry_window_start' => $this->entry_window_start?->format('H:i'),
            'entry_start' => $this->entry_start?->format('H:i'),
            'late_until' => $this->late_until?->format('H:i'),
            'exit_time' => $this->exit_time?->format('H:i'),
            'active' => (bool) $this->active,
        ];
    }
}
