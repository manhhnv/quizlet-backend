<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Term;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
    public function getCorrectAnswer($term_id) {
        return DB::table('testing')
            ->select('answer as item')
            ->where('term_id', '=', $term_id)
            ->first();
    }
    public function getWrongAnswer($term_id) {
        return DB::table('testing')
            ->select('answer as item')
            ->where('term_id', '<>', $term_id)
            ->limit(3)
            ->get();
    }
    public function getQuestions($module_id) {
        try {
            $user = Auth::user();
            $module = Module::find($module_id);
            if ($user != null && $module != null) {
                if ($user->id == $module->user->id || $module->public != 0) {
                    $terms = $module->terms;
                    $set = array();
                    foreach ($terms as $key => $value) {
                        $data = [];
                        $term_id = $terms[$key]->id;
                        $correct = $this->getCorrectAnswer($term_id);
                        $wrong = $this->getWrongAnswer($term_id);
                        $res = json_decode($wrong);
                        array_push($res, $correct);
                        $data['question'] = $terms[$key]->question;
                        $data['answer'] = $res;
                        $data['id'] = $term_id;
                        $data['score'] = $terms[$key]->score;
                        array_push($set, $data);
                    }
                    return response()->json($set, 200);
                }
                else {
                    return response()->json([
                        "message" => "You can not access"
                    ], 400);
                }
            }
        }
        catch (\Exception $exception) {
            return response()->json([
                "message" => $exception->getMessage()
            ], 500);
        }
    }
    public function checkAnswer(Request $request) {
        $sets = $request->sets;
        $score = 0;
        foreach ($sets as $key => $value) {
            $term_id = $sets[$key]['id'];
            $term = Term::find($term_id);
            if ($term->explain == $sets[$key]['answer']) {
                $score += (int) $term->score;
            }
        }
        return $score;
    }
}
