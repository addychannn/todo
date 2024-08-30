<?php

namespace App\Models;

use App\Traits\ConvertAlwaysDateTimeToDefaultTimezoneTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lists extends AppModel
{
    use SoftDeletes, ConvertAlwaysDateTimeToDefaultTimezoneTrait;
    protected $shouldHashPersist = true;
    protected $hashColumnName = 'hash';

    protected $fillable = [
        'list_name',
    ];

    protected $hidden = [
        'íd'
    ];

    public function tasks(){
        return $this->hasMany(Task::class,"list_id","hash");
    }
}
