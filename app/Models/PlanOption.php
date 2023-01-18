<?php

namespace App\Models;

use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlanOption extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

     public function plan(){
        return $this->belongsTo(Plan::class);
     }
}
