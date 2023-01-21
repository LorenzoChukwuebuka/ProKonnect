<?php

namespace App\Models;

use App\Models\Qualifications;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserQualification extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function qualifications()
    {
        return $this->belongsTo(Qualifications::class, 'qualification_id');
    }
}
