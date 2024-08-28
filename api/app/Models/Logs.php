<?php

namespace App\Models;

class Logs extends AppModel
{
    protected $fillable = [
        'user_id',
        'actor',
        'action',
        'type',
        'level',
        'old_data',
        'new_data',
        'module',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
