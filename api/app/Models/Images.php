<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\ImageDeletingTrait;

class Images extends AppModel
{
    use SoftDeletes, ImageDeletingTrait;

    protected $fillable = [
        'name',
        'alt',
        'path',
        'mime',
        'extension',
        'status',
        'main',
        'gallery_id',
    ];

    protected $casts = [
        'main' => 'boolean'
    ];

    public function profile(){
        return $this->hasOne(Profile::class);
    }

    public function gallery(){
        return $this->belongsTo(Gallery::class);
    }
}
