<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function payer()
    {
        return $this->belongsTo(User::class, 'payer_id');
    }

    public function proguide()
    {
        return $this->belongsTo(User::class, 'proguide_id');
    }
}
