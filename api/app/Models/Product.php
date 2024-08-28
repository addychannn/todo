<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Veelasky\LaravelHashId\Eloquent\HashableId;

class Product extends AppModel
{
    use HasFactory, HashableId, SoftDeletes;
    protected $shouldHashPersist = true;
    protected $hashColumnName = 'hash';

    protected $fillable = [
        'name',
        'brand_id',
        'color_id',
    ];

    protected $hidden = [
        'id',
    ];

 
}
