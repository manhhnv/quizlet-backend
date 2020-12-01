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
            ->select('answer')
            ->where('term_id', '=', $term_id)
            ->first();
    }
    public function getWrongAnswer($term_id) {
        return DB::table('testing')
            ->select('answer')
            ->where('term_id', '<>', $term_id)
            ->limit(3)
            ->get();
    }
    public function getQuestions($module_id) {
        $user = Auth::user();
        $module = Module::find($module_id);
        if ($user != null && $module != null) {
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
                array_push($set, $data);
            }
            return response()->json($set);
        }
    }
}
