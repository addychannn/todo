<?php

namespace App\Models;

use App\Traits\ConvertAlwaysDateTimeToDefaultTimezoneTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends AppModel
{
    use SoftDeletes, ConvertAlwaysDateTimeToDefaultTimezoneTrait;
    protected $shouldHashPersist = true;
    protected $hashColumnName= 'hash';

    protected $fillable = [
        'taskName',
        'description',
        'status',
    ];

    protected $hidden = [
        'id'
    ];
}
