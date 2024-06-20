<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LineNotify extends Model
{
    public function __construct()
    {
        $TableName = "line_record";
        $this->setTable($TableName);
    }
    protected $guarded = [];
}
