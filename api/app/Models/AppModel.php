<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Veelasky\LaravelHashId\Eloquent\HashableId;

use App\Traits\ConvertAlwaysDateTimeToDefaultTimezoneTrait;

class AppModel extends Model
{
    use HasFactory, HashableId, ConvertAlwaysDateTimeToDefaultTimezoneTrait;
}