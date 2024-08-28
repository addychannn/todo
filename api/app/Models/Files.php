<?php

namespace App\Models;

class Files extends AppModel
{
    protected $fillable = [
        'name',
        'file_name',
        'path',
        'ext',
        'mime',
        'size',
        'hash_md5',
        'hash_sha1',
        'hash_sha256',
    ];
    
    protected $hidden = [
        'path',
    ];
}
