<?php

namespace App\Models;

use App\Traits\Helpers;

class Gallery extends AppModel
{
    use Helpers;

    protected $fillable = [
        'name',
        'slug',
        'status',
    ];

    protected static function boot(){
        parent::boot();
        $self = new self;

        self::creating(function ($model) use ($self){
            $model->slug = $self->createSlug($model->name, 'Gallery');
        });
    }
    
    public function images(){
        return $this->hasMany(Images::class, 'gallery_id');
    }
}
