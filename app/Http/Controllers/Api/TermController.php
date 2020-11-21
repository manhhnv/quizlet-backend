<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Term;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TermController extends Controller
{
    public function index($id) {
        $term = Term::find($id);
        return response()->json($term);
    }
    public function getTermByModule($module_id) {
        try {
            $module = Module::find($module_id);
            $terms = $module->terms;
            return response($terms, 200);
        }
        catch (\Exception $exception) {
            return response()->json([
                'message' => 'Not found module'
            ], 500);
        }
    }
    public function create(Request $request) {
        $this->validate(
            $request,
            [
                'question' => 'required | string',
                'explain' => 'required | string',
                'score' => 'integer',
                'module_id' => 'required | integer'
            ],
            [
                'question.required' => 'Question can not be blank !',
                'explain.required' => 'Explain can not be blank !',
                'module_id.required' => 'Module ID can not be blank !'
            ]
        );
        $user = Auth::user();
        $module_id = (int) $request->module_id;
        $module = Module::find($module_id);
        $module_user_id = $module->user->id;
        if ($user->id == $module_user_id) {
            $current_time = getCurrentTime();
            $term_data = [
                'question' => htmlspecialchars($request->question),
                'explain' => htmlspecialchars($request->explain),
                'score' => (int) $request->score,
                'module_id' => $module_id,
                'created_at' => $current_time,
                'updated_at' => $current_time
            ];
            try {
                $term = Term::create($term_data);
                return response()->json($term, 200);
            }
            catch (\Exception $exception) {
                return response()->json([
                    'message' => 'Create term failed'
                ], 500);
            }
        }
        else {
            return response()->json([
                'message' => 'Can not found module !'
            ], 500);
        }
    }
    public function update($module_id, $term_id, Request $request) {
        $this->validate(
            $request,
            [
                'question' => 'string|nullable',
                'explain' => 'string|nullable',
                'score' => 'integer|nullable'
            ]
        );
        $term = Term::find($term_id);
        if ($term && $term->module->id == $module_id) {
            $update_data = [
                'question' => isset($request->question) ? htmlspecialchars($request->question) : $term->question,
                'explain' => isset($request->explain) ? htmlspecialchars($request->explain) : $term->explain,
                'score' => isset($request->score) ? (int) $request->score : $term->score,
                'updated_at' => getCurrentTime()
            ];
            try {
                $term->update($update_data);
                return $this->index($term_id);
            }
            catch (\Exception $exception) {
                return response()->json([
                    'message' => 'Not found'
                ], 500);
            }
        }
        else {
            return response()->json([
                'message' => 'Term not found in your module'
            ], 500);
        }
    }
    public function delete($module_id, $term_id) {
        try {
            $term = Term::find($term_id);
            $term->delete();
            $module = Module::find($module_id);
            $terms = $module->terms;
            return response()->json($terms, 200);
        }
        catch (\Exception $exception) {
            return response()->json([
                'message' => 'Term not found'
            ], 500);
        }
    }
}
