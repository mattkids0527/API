<?php

namespace App\Models\Daily;

use Illuminate\Database\Eloquent\Model;

class DailyUnit extends Model
{
	public function __construct()
	{
		$TableName = "daily_unit";
		$this->setTable($TableName);
	}
}
