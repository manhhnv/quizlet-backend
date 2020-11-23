<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClassHasFolder;
use App\Models\ClassHasModule;
use App\Models\ClassModel;
use App\Models\Folder;
use App\Models\Module;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ClassController extends Controller
{
    public function all() {
        $user = Auth::user();
        try {
            $classes = User::find($user->id)->classes;
            return response()->json($classes, 200);
        }
        catch (\Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage()
            ], 500);
        }
    }
    public function create(Request $request) {
        $user = Auth::user();

        $this->validate(
            $request,
            [
                'name' => 'required | string',
                'public' => 'integer',
            ],
            [
                'name.required' => 'Class name can not be blank !',
            ]
        );
        if ($user) {
            $current_time = getCurrentTime();
            $code = Str::random(16);
            $class_data = [
                'name' => htmlspecialchars($request->name),
                'public' => (int) $request->public,
                'user_id' => $user['id'],
                'code' => $code,
                'created_at' => $current_time,
                'updated_at' => $current_time
            ];
            try {
                $class = ClassModel::create($class_data);
                return response()->json($class, 200);
            }
            catch (\Exception $exception) {
                return response()->json([
                    "message" => $exception->getMessage()
                ], 500);
            }
        }
    }
    public function update($id, Request $request) {
        $query = $request->query();
        $user = Auth::user();
        if ($user) {
            $current_time = getCurrentTime();
            $class = ClassModel::where('id', $id)->where('user_id', $user->id)->first();
            if ($class) {
                $class_update_data = [
                    'name' => isset($query['name']) ? htmlspecialchars($query['name']) : $class->name,
                    'public' => isset($query['public']) ? (int) $query['public'] : $class->public,
                    'updated_at' => $current_time
                ];
                try {
                    ClassModel::find($class->id)
                        ->update($class_update_data);
                    return response()->json(ClassModel::find($class->id), 200);
                }
                catch (\Exception $exception) {
                    return response()->json([
                        "message" => $exception->getMessage()
                    ], 500);
                }
            }
            else {
                return response()->json([
                    'message' => 'Class not found'
                ], 500);
            }
        }
    }
    public function delete($id) {
        $user = Auth::user();
        try {
            $class = ClassModel::find($id);
            $class_user_id = $class->user->id;
            if ($user->id == $class_user_id) {
                $class->delete();
            }
            return response()->json([
                'message' => 'Deleted success'
            ], 200);
        }
        catch (\Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage()
            ], 500);
        }
    }

    public function modules(Request $request) {
        $req_class_id = (int) $request->query('class_id');
        if ($req_class_id) {
            $class = ClassModel::find($req_class_id);
            if ($class->public) {
                try {
                    $modules = DB::table('module')
                        ->join('class_has_module', 'module.id', '=', 'class_has_module.module_id')
                        ->where('class_has_module.class_id', '=', $req_class_id)
                        ->select('module.*')
                        ->get();
                    return response($modules, 200);
                }
                catch (\Exception $exception) {
                    return response()->json([
                        'message' => 'Something wrong !'
                    ], 500);
                }
            }
            else {
                return response()->json([
                    'message' => 'Class not found'
                ], 500);
            }
        }
    }
    public function assignModule($module_id, $class_id) {
        $user = Auth::user();
        if ($user) {
            $module = Module::find($module_id);
            $class = ClassModel::find($class_id);
            $module_user_id = $module->user->id;
            $class_user_id = $class->user->id;
            if ($user->id == $module_user_id && $user->id == $class_user_id) {
                $current_time = getCurrentTime();
                $data = [
                    'module_id' => (int) $module_id,
                    'class_id' => (int) $class_id,
                    'created_at' => $current_time,
                    'updated_at' => $current_time
                ];
                try {
                    $instance = ClassHasModule::create($data);
                    return response()->json($instance, 200);
                }
                catch (\Exception $exception) {
                    return response([
                        'message' => "Assign module to class failed !"
                    ], 500);
                }
            }
        }
    }
    public function deleteModule(Request $request) {
        $user = Auth::user();
        $req_module_id = (int) $request->query('module_id');
        $req_class_id = (int) $request->query('class_id');
        if ($req_module_id && $req_class_id) {
            $class = ClassModel::find($req_class_id);
            $class_user_id = $class->user->id;
            if ($class_user_id == $user->id) {
                try {
                    ClassHasModule::where('class_id', '=', $req_class_id)
                        ->where('module_id', '=', $req_module_id)
                        ->delete();
                    return $this->modules($request);
                }
                catch (\Exception $exception) {
                    return response()->json(['message' => 'Not found'], 500);
                }
            }
        }
    }
    public function getAllFolderInClass($class_id, $code) {
        $class = ClassModel::find($class_id);
        if ($class->code == $code) {
            try {
                $folders = DB::table('folder')
                    ->join('class_has_folder', 'class_has_folder.folder_id', '=', 'folder.id')
                    ->where('class_has_folder.class_id', '=', $class_id)
                    ->select('folder.*')
                    ->get();
                return response()->json($folders, 200);
            }
            catch (\Exception $exception) {
                return response()->json([
                    "message" => $exception->getMessage()
                ], 500);
            }
        }
        else {
            return response()->json([
                "message" => 'Can not get folders in this class'
            ], 500);
        }
    }
    public function addFolderToClass($class_id, $folder_id, $code) {
        $class = ClassModel::find($class_id);
        $folder = Folder::find($folder_id);
        $user = Auth::user();
        if ($class->code == $code && $folder->user->id == $user->id && $class->user->id == $user->id) {
            $current_time = getCurrentTime();
            $data = [
                'folder_id' => $folder_id,
                'class_id' => $class_id,
                'created_at' => $current_time,
                'updated_at' => $current_time
            ];
            try {
                $instance = ClassHasFolder::create($data);
                return $this->getAllFolderInClass($class_id, $code);
            }
            catch (\Exception $exception) {
                return response()->json([
                    "message" => $exception->getMessage()
                ], 500);
            }
        }
        else {
            return response()->json([
                "message" => 'Can not find class, folder'
            ], 500);
        }
    }
    public function deleteFolderFromClass($class_id, $folder_id, $code) {
        $class = ClassModel::find($class_id);
        $folder = Folder::find($folder_id);
        $user = Auth::user();
        if ($class->code == $code && $folder->user->id == $user->id && $class->user->id == $user->id) {
            try {
                ClassHasFolder::where('class_id', '=', $class_id)
                    ->where('folder_id', '=', $folder_id)
                    ->delete();
                return $this->getAllFolderInClass($class_id, $code);
            }
            catch (\Exception $exception) {
                return response()->json([
                    "message" => $exception->getMessage()
                ], 500);
            }
        }
        else {
            return response()->json([
                "message" => "Not found"
            ], 500);
        }
    }
}
