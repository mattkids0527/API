<?php

namespace App\Models\Daily;

use Illuminate\Database\Eloquent\Model;

class DailyArticle extends Model
{
	public function __construct()
	{
		$TableName = "daily_article";
		$this->setTable($TableName);
	}
}
