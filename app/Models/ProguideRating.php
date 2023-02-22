<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProguideRating extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user_rated()
    {
        return $this->belongsTo(User::class, 'proguide_id');
    }

    public function rated_by()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
