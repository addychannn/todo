<?php

namespace App\Models;

class SessionModel extends AppModel {
    public $timestamps = false;
    protected $table = "sessions";
    protected $fillable = ["user_id", "ip_address", "user_agent", "payload", "last_activity"];
}
