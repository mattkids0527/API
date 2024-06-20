<?php

namespace App\Models\Daily;

use Illuminate\Database\Eloquent\Model;

class DailyStaff extends Model
{
	public function __construct()
	{
		$TableName = "daily_staff";
		$this->setTable($TableName);
	}
}
