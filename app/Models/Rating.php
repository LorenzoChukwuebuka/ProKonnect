<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user_review()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function user_rated()
    {
        return $this->belongsTo(User::class, 'user_rated');
    }

}
