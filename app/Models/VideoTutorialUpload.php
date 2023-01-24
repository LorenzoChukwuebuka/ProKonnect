<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VideoTutorialUpload extends Model
{
    use HasFactory,softDeletes;

    protected $guarded = [];

    public function proguide(){
        return $this->belongsTo(User::class,'proguide_id');
    }
}
