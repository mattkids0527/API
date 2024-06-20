<?php

namespace App\Models\Daily;

use Config;
use BaseFunction;
use Session;
use DB;
use Request;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as Model_Builder;

use Illuminate\Database\Eloquent\Model;

class DailyProject extends Model
{
	public function __construct()
	{
		$TableName = "daily_project";
		$this->setTable($TableName);
	}
}
