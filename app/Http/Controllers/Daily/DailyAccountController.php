<?php

namespace App\Http\Controllers\Daily;

use App\Http\Controllers\Controller;
use App\Models\Daily\DailyAccount;
use App\Models\Daily\DailyArticle;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DB;
use Validator;
use View;
use JWTAuth;
use Exception;

class DailyAccountController extends Controller
{
    public function __construct()
    {
        // 
    }

    private $field = ['password', 'two_password', 'account', 'level'];

    public function index(Request $request)
    {
        $action = $request->action ?? 'all';
        $now_user = auth()->user();
        // $account_id = intval($now_user->account_id);

        if ($action != 'all') {
            $account = DailyAccount::where('account_id', $action)->first();

            if ($account == null) {
                return response()->json("查無帳號!!", 401);
            }
        }


        if ($action == "all") {
            $account = DailyAccount::all();
            return response()->json($account);
        } else if ($action != "all") {
            return response()->json($account);
        }
    }
    public function create(Request $request)
    {

        $data = $request->data;

        $rule = [
            'password' => [
                'required',
                'min:8',
                'regex:/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/',
            ],
            'level' => 'numeric|between:1,3',
            'two_password' => 'required|same:password',
            'account' => 'required',
        ];
        $message = [
            'password.required' => '密碼為必填',
            'password.min' => '密碼至少8位字符',
            'password.regex' => '密碼必須包含一個數字、英文字母和特殊符號',
            'two_password.required' => '二次密碼為必填',
            'two_password.same' => '二次密碼與密碼不同',
            'account.required' => '登入帳號為必填',
            // 'account_id.required' => '員工編號為必填',
            // 'account_id.numeric' => '員工編號必須為數字',
            'level.numeric' => '必須為數字',
            'level.between' => '範圍為1-3 ,[ 1=>Boss,2=>Management,3=>Staff ]'
        ];

        $res = [];

        $validator = Validator::make($data, $rule, $message);
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $message) {
                $res['message'][] = $message . ';';
            }
            $res['is_pass'] = false;
        } else {
            $res['message'] = [];
            $res['is_pass'] = true;
        }

        $res['pass'] = array_values(array_diff($this->field, $validator->errors()->keys()));
        $res['fail'] = $validator->errors()->keys();

        if ($res['is_pass']) {

            $account = new DailyAccount;
            $accound_id = $account->orderby('id', 'desc')->first()->account_id + 1;

            if (count($account->where('account', $data['account'])->get()) > 0) {
                return response()->json($data['account'] . " 已有人註冊，請重新填入帳號", 400);
            }

            $account->name = $data['name'];
            $account->account = $data['account'];
            $account->password = Hash::make($data['password']);
            $account->level = $data['level'] ?? 3;
            $account->account_id = $accound_id;
            $account->save();

            return response()->json([
                'Create Success !' => $account
            ], 201);
        } else {
            return response()->json($res, 400);
        }
    }
    public function edit(Request $request)
    {
        $account_id = intval($request->account_id);
        $account = DailyAccount::where('account_id', $account_id)->first();

        if ($account === null) return response()->json(["錯誤！資料庫找不到該員工編號 :" . $account_id], 401);

        $edit = $request->data;
        $edit['id'] = intval($account['id']);
        $edit['account_id'] = intval($account_id);

        $rule = [
            'password' => [
                'min:8',
            ],
            'two_password' => 'required_with:password|same:password',
            'level' => [
                'numeric',
                'between:1,3',
            ],
            'account_id' => 'numeric'
        ];
        $message = [
            'password.min' => '密碼至少8位字符',
            'two_password.same' => '二次密碼與密碼不同',
            'two_password.required_with' => '二次密碼必須填入',
            'level.numeric' => '必須為數字',
            'level.between' => '範圍為1-3 ,[ 1=>Boss,2=>Management,3=>Staff ]',
            'account_id.numeric' => '必須為數字',
        ];

        $res = [];

        $validator = Validator::make($edit, $rule, $message);
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $message) {
                $res['message'][] = $message . ';';
            }
            $res['is_pass'] = false;
        } else {
            $res['message'] = [];
            $res['is_pass'] = true;
        }

        $res['pass'] = array_values(array_diff($this->field, $validator->errors()->keys()));
        $res['fail'] = $validator->errors()->keys();

        if ($res['is_pass']) {

            try {
                if (!empty($edit['two_password'])) unset($edit['two_password']);
                if (!empty($edit['password'])) $edit['password'] = Hash::make($edit['password']);

                $account::upsert($edit, ['id']);
            } catch (Exception $e) {
                return response()->json(["Update Failed." => $e], 400);
            }
            return response()->json(["Update Success"], 201);
        } else {
            return response()->json($res, 400);
        }
    }

    public function delete(Request $request)
    {
        $account_id = intval($request->account_id);
        $account = DailyAccount::where('account_id', $account_id)->first();

        if ($account === null) return response()->json(["id:" . $account_id . " not found !"], 401);

        $now_account_level = intval(auth()->user()->level);
        if ($now_account_level !== 1 && $now_account_level !== 2) {
            return response()->json(["此帳號無權限刪除，請聯絡管理員！"], 401);
        } else {
            try {
                $account->delete();
            } catch (Exception $e) {
                return response()->json(["Delete Failed." => $e], 400);
            }
            return response()->json([
                "ID:" . $account_id . " Delete Success !"
            ]);
        }
    }
}
