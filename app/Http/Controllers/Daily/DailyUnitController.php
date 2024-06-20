<?php

namespace App\Http\Controllers\Daily;

use App\Http\Controllers\Controller;
use App\Models\Daily\DailyUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DB;
use Validator;
use View;
use JWTAuth;
use Exception;

class DailyUnitController extends Controller
{
    public function __construct()
    {
        // 
    }

    private $field = ['title'];

    public function index(Request $request)
    {
        $unit_id = !empty($request->unit_id) ? intval($request->unit_id) : null;

        if ($unit_id === null) {

            $unit = DailyUnit::all();
        } else {

            $unit = DailyUnit::find($unit_id);

            if ($unit === null) return response()->json(["id not found !"], 401);
        }
        return response()->json($unit);
    }
    public function create(Request $request)
    {
        $data = $request->all();

        $rule = [
            'title' => 'required|max:255',
        ];
        $message = [
            'title.required' => '請輸入單元名稱',
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
            $unit = new DailyUnit;
            $unit->title = $data['title'];
            $unit->save();

            return response()->json(['Create Successful !', $unit], 201);
        } else {
            return response()->json($res, 400);
        }
    }
    public function edit(Request $request)
    {
        $id = $request->id;
        $edit = $request->data;
        $edit['id'] = intval($id);
        $unit = DailyUnit::find($id);

        if ($unit === null) return response()->json(["Unit not found !"], 401);
        try {
            $unit::upsert($edit, ['id']);
        } catch (Exception $e) {
            return response()->json(["Update Failed." => $e], 400);
        }
        return response()->json(["Update Success !"]);
    }

    public function delete(Request $request)
    {
        $id = $request->id;
        $unit = DailyUnit::find($id);
        if ($unit === null) return response()->json(["id:" . $id . " not found !"], 401);

        try {
            $unit->delete();
        } catch (Exception $e) {
            return response()->json(["Delete Failed." => $e], 400);
        }
        return response()->json([
            "ID:" . $id . " Delete Success !"
        ]);
    }
}
