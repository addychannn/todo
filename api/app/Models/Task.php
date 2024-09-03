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
        'list_id',
        'task_name',
        'status',

    ];

    protected $hidden = [
        'id'
    ];

    protected $cast = [
        'task_name' => 'array',
    ];

    public function lists(){
        return $this->hasOne(Lists::class,"hash","list_id");
    }

    
}
