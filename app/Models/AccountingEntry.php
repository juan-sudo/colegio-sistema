<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountingEntry extends Model
{
    protected $fillable = [
        'type', 'category', 'description', 'amount', 'date', 'reference', 'notes'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date',
    ];

    public function scopeIncome($query)
    {
        return $query->where('type', 'ingreso');
    }

    public function scopeExpense($query)
    {
        return $query->where('type', 'egreso');
    }

    public function scopeFixedCost($query)
    {
        return $query->where('type', 'gasto_fijo');
    }
}
