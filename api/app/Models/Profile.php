<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphMany;

class Profile extends AppModel
{

    protected $fillable = [
        'user_id',
        'first_name',
        'middle_name',
        'last_name',
        'gender_id',
        'suffix',
        'nickname',
        'birthdate',
        'image_id'
    ];
    
    protected $casts = [
        "birthdate" => "date",
    ];

    public function addresses(): MorphMany{
        return $this->morphMany(Address::class, 'addressable');
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function gender() {
        return $this->belongsTo(Gender::class, 'gender_id');
    }

    public function image(){
        return $this->belongsTo(Images::class);
    }

    public function images(){
        return $this->belongsToMany(Images::class, 'profile_images', 'profile_id', 'image_id');
    }
}
