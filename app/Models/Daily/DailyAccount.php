<?php

namespace App\Models\Daily;

use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;

class DailyAccount extends Authenticatable implements JWTsubject
{

    use Notifiable;
    public function __construct()
    {
        parent::__construct();

        $TableName = "daily_account";
        $this->setTable($TableName);
    }
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
