<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Group;
use App\Models\ProguideRating;
use App\Models\Project;
use App\Models\Referal;
use App\Models\Socials;
use App\Models\UserGroup;
use App\Models\UserInterests;
use App\Models\UserQualification;
use App\Models\Wallet;
use App\Models\WithdrawalRequest;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'country_id' => 'integer',
        'university_id' => 'integer',
        'bad_word_count' => 'integer',
    ];

    public function userinterests()
    {
        return $this->hasMany(UserInterests::class);
    }

    public function userqualification()
    {
        return $this->hasMany(UserQualification::class);
    }

    public function userspecialization()
    {
        return $this->hasMany(UserSpecialization::class);
    }

    public function referal()
    {
        return $this->hasMany(Referal::class);
    }

    public function project()
    {
        return $this->hasMany(Project::class);
    }

    public function bank_details()
    {
        return $this->hasOne(BankDetails::class);
    }

    public function withdrawalRequest()
    {
        return $this->hasMany(WithdrawalRequest::class);
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function group()
    {
        return $this->hasMany(Group::class);
    }

    public function user_group()
    {
        return $this->hasMany(UserGroup::class);
    }

    public function group_messages()
    {
        return $this->hasMany(GroupMessage::class);
    }

    public function socials()
    {
        return $this->hasMany(Socials::class);
    }

    public function review()
    {
        return $this->hasMany(ProguideRating::class);
    }

    public function proguideConnection(){
        return $this->hasMany(StudentsProguide::class,'proguide_id');
    }


}
