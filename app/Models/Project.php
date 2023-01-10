<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use WisdomDiala\Countrypkg\Models\Country;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function proguide()
    {
        return $this->belongsTo(User::class,'proguide_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
