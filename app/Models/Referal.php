<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Referal extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function referee()
    {
        return $this->belongsTo(User::class, 'referee_id');
    }

    public function referal()
    {
        return $this->belongsTo(User::class, 'referal_id');
    }
}
