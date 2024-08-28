<?php

namespace App\Traits;
use Illuminate\Http\Request;

trait LikeToggleTrait
{
    protected static function LikeToggle() {
        return env("DB_CONNECTION", 'mysql') == 'pgsql' ? "ILIKE" : "LIKE";
    }
}