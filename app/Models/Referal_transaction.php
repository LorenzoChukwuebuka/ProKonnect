<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Referal_transaction extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user_referal(){
        return $this->belongsTo(User::class,'user_referred');
    }
}
