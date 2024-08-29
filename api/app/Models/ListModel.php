
<?php

namespace App\Models;

use App\Traits\ConvertAlwaysDateTimeToDefaultTimezoneTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class ListModel extends AppModel
{
    use SoftDeletes, ConvertAlwaysDateTimeToDefaultTimezoneTrait;
    protected $shouldHashPersist = true;
    protected $hashColumnName = 'hash';

    protected $fillable = [
        'listName'
    ];

    protected $hidden = [
        'id'
    ];
}
