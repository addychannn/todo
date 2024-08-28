<?php

namespace App\Models;

use App\Traits\LogModelTrait as Logger;

class AccountStatusHistory extends AppModel
{
    use Logger;

    protected $log_module_name = "AccountStatusHistory";

    protected $fillable = [
        'user_id',
        'status',
        'reason'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
