<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentsProguide extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function student()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function proguide()
    {
        return $this->belongsTo(User::class, 'proguide_id');
    }
}
