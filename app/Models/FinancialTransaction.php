<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class FinancialTransaction extends Model { protected $fillable=['type','category','description','amount','transaction_date','payment_id','recorded_by']; protected $casts=['amount'=>'decimal:2','transaction_date'=>'date']; }
