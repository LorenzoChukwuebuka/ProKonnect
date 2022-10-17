<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Chats extends Model
{
    use HasFactory,SoftDeletes;

    protected $guarded = [];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id')->select('id','full_name','profile_image');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id')->select('id','full_name','profile_image');
    }
}
