<?php

namespace App\Http\Controllers\Daily;

use App\Http\Controllers\Controller;
use App\Models\Daily\DailyProject;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DB;
use Validator;
use View;
use JWTAuth;
use Exception;

class DailyProjectController extends Controller
{
    public function __construct()
    {
        // 
    }

    private $field = ['title'];

    public function index(Request $request)
    {

        $project_id = !empty($request->project_id) ? intval($request->project_id) : null;

        if ($project_id === null) {

            $project = DailyProject::all();
        } else {

            $project = DailyProject::find($project_id);

            if ($project === null) return response()->json(["id not found !"], 401);
        }
        return response()->json($project);
    }
    public function create(Request $request)
    {
        $data = $request->data;

        $rule = [
            'title' => 'required|max:255',
        ];
        $message = [
            'title.required' => '請輸入專案名稱',
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
            $project = new DailyProject;
            $project->title = $data['title'];
            $project->w_rank = $data['w_rank'] ?? 0;
            $project->save();

            return response()->json(['Create Successful !', $project], 201);
        } else {
            return response()->json($res, 400);
        }
    }

    public function edit(Request $request)
    {
        $id = $request->id;
        $edit = $request->data;
        $edit['id'] = intval($id);

        $project = DailyProject::find($id);

        if ($project === null) return response()->json(["id not found !"], 401);
        try {
            $project::upsert($edit, ['id']);
        } catch (Exception $e) {
            return response()->json(["Update Failed." => $e], 400);
        }
        return response()->json(["Update Success !"]);
    }

    public function delete(Request $request)
    {
        $id = $request->id;
        $project = DailyProject::find($id);
        if ($project === null) return response()->json(["id not found !"], 401);

        try {
            $project->delete();
        } catch (Exception $e) {
            return response()->json(["Delete Failed." => $e], 400);
        }
        return response()->json([
            "ID:" . $id . " Delete Success !"
        ]);
    }
}
