<?php

namespace App\Http\Controllers\Daily;

use App\Http\Controllers\Controller;
use App\Models\Daily\DailyArticle;
use App\Models\Daily\DailyProject;
use App\Models\Daily\DailyUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DB;
use Validator;
use View;
use JWTAuth;
use Exception;

class DailyArticleController extends Controller
{
    public function __construct()
    {
        // 
    }

    private $field = ['project_id', 'unit_id'];

    public function index(Request $request)
    {

        $article_id = !empty($request->article_id) ? intval($request->article_id) : null;

        if ($article_id === null) {

            $article = DailyArticle::all();
        } else {

            $article = DailyArticle::find($article_id);

            if ($article === null) return response()->json(["id not found !"], 401);
        }
        return response()->json($article);
    }
    public function create(Request $request)
    {
        $data = $request->data;
        $user = auth()->user();
        // dd($data);
        $rule = [
            'project_id' => 'required',
            'unit_id' => 'required',
        ];
        $message = [
            'project_id.required' => '專案為必填',
            'unit_id.required' => '單元為必填',
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
            $project = DailyProject::find($data['project_id']);
            $unit = DailyUnit::find($data['unit_id']);

            try {

                $article = new DailyArticle;
                $article->title = $data['title'] ?? '';
                $article->belongs_account = $user->account;
                $article->content = $data['content'] ?? '';
                $article->project_id = $data['project_id'];
                $article->unit_id = $data['unit_id'];
                $article->project_title = $project->title;
                $article->unit_title = $unit->title;
                $article->time = $data['time'] ?? '';
                $article->save();
            } catch (Exception $e) {
                return response()->json(['Create Failed !' => $e], 401);
            }
            return response()->json(['Create Successful !', $article], 201);
        } else {
            return response()->json($res, 400);
        }
    }
    public function edit(Request $request)
    {


        $id = $request->id;
        $edit = $request->data;
        $article = DailyArticle::find($id);
        if ($article  === null) return response()->json(["id not found !"], 401);

        $project = DailyProject::find($edit['project_id']);
        $unit = DailyUnit::find($edit['unit_id']);

        $user = auth()->user();

        $edit['id'] = intval($id);
        $edit['w_rank'] = intval($edit['w_rank'] ?? 0);
        $edit['project_id'] = intval($edit['project_id'] ?? 1);
        $edit['unit_id'] = intval($edit['unit_id'] ?? 1);
        $edit['time'] = $edit['time'] ?? 0;
        $edit['project_title'] = $project->title;
        $edit['unit_title'] = $unit->title;
        $edit['belongs_account'] = $user->account;

        try {

            $article::upsert($edit, ['id']);
        } catch (Exception $e) {
            return response()->json(["Update Failed." => $e], 400);
        }
        return response()->json(["Update Success !" => $edit]);
    }

    public function delete(Request $request)
    {
        $id = $request->id;
        $project = DailyArticle::find($id);
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
