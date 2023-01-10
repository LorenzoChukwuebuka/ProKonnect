<?php

namespace App\Models;

use App\Models\UserInterests;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Interests extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];

    public function user_interests()
    {
        return $this->hasMany(UserInterests::class);
    }
}
