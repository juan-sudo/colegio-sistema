<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class PaymentConcept extends Model { protected $fillable=['school_year_id','name','type','amount','due_day','active']; protected $casts=['amount'=>'decimal:2','active'=>'boolean']; }
