<?php

namespace App\Http\Controllers\LineNotify;

use App\Services\LineNotify\LineNotify;
use Http;
use Illuminate\Http\Request;
use View;
use App\Http\Controllers\Controller;
use App\Models\LineNotify as ModelLineNotify;
use Illuminate\Support\Facades\Redirect;

class LineNotifyController extends Controller
{

	// line notify需要事先到官網去註冊 : https://notify-bot.line.me/zh_TW/
	// line API文件 : https://notify-bot.line.me/doc/en/
	private static $client_id = 'VVnIskDzOtTshjQBohZ0nj'; // Client ID (需先在官網註冊取得)
	private static $client_secret = 'M8nf2RoGKJCmhkTeJP5IdZabOVUEHlkgXxeGt4ASwli'; // Client Secret (需先在官網註冊取得)
	private static $redirect_uri = 'http://220.133.185.50/linenotify/getToken'; // Callback URL (需先在官網綁定)
	private static $notify;

	public function __construct()
	{
		self::$notify = new LineNotify(
			self::$client_id,
			self::$client_secret,
			self::$redirect_uri
		);
	}

	public function index()
	{
		// 步驟一 : 跳轉去line notify頁面進行連動
		return redirect(self::$notify->requestLineNotify());
	}

	public function sendClient(Request $request)
	{
		$client = $request->client;

		$record = ModelLineNotify::where('client_id', $client)->get();

		if (count($record) == 0) return Redirect('http://220.133.185.50/linenotify');

		$result = [];
		$result['access_token'] = $record->first()->access_token;
		$result['client_id'] = $record->first()->client_id;
		$result['SendMessageULI'] = $record->first()->SendMessageULI;

		return view('linenotify.index', ['result' => $result, 'record' => $record]);
	}

	public function getToken(Request $request)
	{
		$code = $request->code;
		$state = $request->state;

		// 步驟二 : 連動完取得Token以及發訊息的API網址
		// 取得token API網址,可以參閱API文件 搜尋=> https://notify-bot.line.me/oauth/token
		$result = self::$notify->responseToken($code, $state);
		if ($result['status'] != 'fail') {

			$record = collect();
			return view('linenotify.index', ['result' => $result, 'record' => $record]);
		} else {
			return response()->json($result);
		};
	}

	public function sendMessage(Request $request)
	{

		$message = $request->text;
		$access_token = $request->data['access_token'];
		// $imageFullsize = $request->imageFullsize;
		// $imageThumbnail = $request->imageThumbnail;


		// 步驟三 : 發送訊息或是圖片
		// 發訊息的API網址,可以參閱API文件 搜尋=> https://notify-api.line.me/api/notify

		// access_token(必須)
		// message(要發送的訊息)
		// imageFullsize & imageThumbnail(要發送的圖片,必須是網址)
		$result = self::$notify->sendLineNotifymessage($access_token, $message);

		if ($result['status'] == '200') {
			ModelLineNotify::create([
				'client_id' => $request->data['client_id'],
				'text' => $message,
				'access_token' => $request->data['access_token'],
				'SendMessageULI' => $request->data['SendMessageULI']
			]);

			$record = ModelLineNotify::where('client_id', $request->data['client_id'])->get();
			return response()->json([
				'result' => $result,
				'view' => view('linenotify._table', ['record' => $record])->render(),
			], 200);
		} else {
			return response()->json([
				'result' => $result,
			], 400);
		}
	}

	public function status(Request $request)
	{
		// 步驟四(非必要) : 查看目前API使用次數等等
		// 發訊息的API網址,可以參閱API文件 搜尋=> https://notify-api.line.me/api/status

		$access_token = $request->access_token;
		return self::$notify->checkLineNotifystatus($access_token);
	}
}
