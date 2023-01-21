<?php

namespace App\Models;

use App\Models\User;
use App\Models\Specialization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserSpecialization extends Model
{
    use HasFactory,SoftDeletes;
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function specialization(){
        return $this->belongsTo(Specialization::class,'specialization_id');
    }
}
