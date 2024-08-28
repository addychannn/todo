<?php

namespace App\Models;

class Gender extends AppModel
{

    protected $fillable = [
        'name',
        'description',
        'disabled_at'
    ];

    protected $casts = [
        "disabled_at" => "datetime",
    ];
}
