<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'student_id', 'type', 'amount', 'discount', 'paid',
        'status', 'due_date', 'paid_date', 'payment_method', 'notes'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'discount' => 'decimal:2',
        'paid' => 'decimal:2',
        'due_date' => 'date',
        'paid_date' => 'date',
    ];

    protected $appends = ['balance', 'is_overdue'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function getBalanceAttribute()
    {
        return $this->amount - $this->discount - $this->paid;
    }

    public function getIsOverdueAttribute()
    {
        return $this->status !== 'pagado' && $this->due_date->lt(now());
    }
}
