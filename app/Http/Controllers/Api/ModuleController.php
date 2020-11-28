<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\Folder;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Module;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ModuleController extends Controller
{
    public function index($id) {
        try {
            $module = Module::find($id);
            return response()->json($module, 200);
        }
        catch (\Exception $exception) {
            return response()->json([
                "message" => $exception->getMessage()
            ], 500);
        }
    }
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
                'description' => 'string'
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
                'description' => htmlspecialchars($request->description),
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
                    'updated_at' => $current_time
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
                return $this->allModules();
//                return response()->json([
//                    'message' => 'Deleted success'
//                ], 200);
            }
            catch (\Exception $exception) {
                return response()->json([
                    "message" => $exception->getMessage()
                ], 500);
            }
        }
    }
    public function modulesInFolderService($folder_id) {
        $user = Auth::user();
        try {
            if ($folder_id) {
                $folder = Folder::find($folder_id);
                $folder_user_id = $folder->user->id;
                $modules = DB::table('module')
                    ->join('folder_has_module', 'module_id', '=', 'module.id')
                    ->where('folder_has_module.folder_id', '=', $folder_id);
                if ($folder_user_id == $user->id) {
                    $modules = $modules->select('module.*')->get();
                    return response()->json($modules, 200);
                }
                else {
                    if ($folder->public != 0) {
                        $modules = $modules->where('module.public', '<>', 0)
                            ->select('module.*')
                            ->get();
                        return response()->json($modules, 200);
                    }
                    else {
                        return response()->json([
                            "message" => 'You can not access this folder'
                        ], 400);
                    }
                }
            }
        }
        catch (\Exception $exception) {
            return response()->json([
                "message" => $exception->getMessage()
            ], 500);
        }
        return response()->json([
            "message" => 'Can not access this folder'
        ], 500);
    }
    public function modulesInClassService($class_id) {
        $user = Auth::user();
        try {
            if ($class_id) {
                $class = ClassModel::find($class_id);
                $class_user_id = $class->user->id;
                $modules = DB::table('module')
                    ->join('class_has_module', 'module.id', '=', 'class_has_module.module_id')
                    ->where('class_has_module.class_id', '=', $class_id);
                if ($user->id == $class_user_id) {
                    $modules = $modules->select('module.*')
                        ->get();
                    return response()->json($modules, 200);
                }
                else {
                    if ($class->public != 0) {
                        $modules = $modules->where('module.public', '<>', 0)
                            ->select('module.*')
                            ->get();
                        return response()->json($modules, 200);
                    }
                    else {
                        return response()->json([
                            "message" => 'You can not access this folder'
                        ], 400);
                    }
                }
            }
        }
        catch (\Exception $exception) {
            return response()->json([
                "message" => $exception->getMessage()
            ], 500);
        }
        return response()->json([
            "message" => 'Can not access this class'
        ], 500);
    }
}
