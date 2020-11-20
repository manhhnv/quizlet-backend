<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Module;
use Illuminate\Support\Facades\Auth;

class ModuleController extends Controller
{
    public function allModules() {
        $modules = Module::all();
        return response()->json($modules, 200);
    }
    public function selfModule() {
        try {
            $user = Auth::user();
            if ($user) {
                $modules = User::find($user->id)->modules;
                if ($modules) {
                    return response()->json($modules, 200);
                }
            }
        }
        catch (\Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage()
            ], 500);
        }
    }
    public function create(Request $request) {
        $this->validate(
            $request,
            [
                'name' => 'required | string',
                'public' => 'boolean',
                'max_score' => 'integer',
                'class_id' => 'integer',
                'folder_id' => 'integer'
            ],
            [
                'name.required' => 'Module name can not be blank !',
            ]
        );
        $user = Auth::user();
        if ($user) {
            $current_time = getCurrentTime();
            $module_data = [
                'name' => htmlspecialchars($request->name),
                'max_score' => (int) ($request->max_score),
                'public' => (int) ($request->public),
                'user_id' => $user->id,
                'class_id' => isset($request->class_id) ? (int) $request->class_id : null,
                'folder_id' => isset($request->folder_id) ? (int) $request->folder_id : null,
                'created_at' => $current_time,
                'updated_at' => $current_time,
            ];
            try {
                $module = Module::create($module_data);
                return response()->json($module, 200);
            }
            catch (\Exception $exception) {
                return response()->json([
                    'message' => $exception->getMessage()
                ], 500);
            }
        }
    }
    public function update($id, Request $request) {
        $query = $request->query();
        $user = Auth::user();
        if ($user) {
            $current_time = getCurrentTime();
            $module = Module::where('id', $id)->where('user_id', $user->id)->first();
            if ($module) {
                $module_update_data = [
                    'name' => isset($query['name']) ? htmlspecialchars($query['name']) : $module->name,
                    'public' => isset($query['public']) ? (int) $query['public'] : $module->public,
                ];
                try {
                    Module::find($id)
                        ->update($module_update_data);
                    return response()->json(Module::find($id), 200);
                }
                catch (\Exception $exception) {
                    return response()->json([
                        "message" => $exception->getMessage()
                    ], 500);
                }
            }
            else {
                return response()->json([
                    'message' => 'Module not found'
                ], 404);
            }
        }
    }
    public function delete($id) {
        $user = Auth::user();
        $user_id = Module::find($id)->user->id;
        if ($user_id == $user->id) {
            try {
                Module::find($id)->delete();
                return response()->json([
                    'message' => 'Deleted success'
                ], 200);
            }
            catch (\Exception $exception) {
                return response()->json([
                    "message" => $exception->getMessage()
                ], 500);
            }
        }
    }
}
