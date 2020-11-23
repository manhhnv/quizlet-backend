<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Folder;
use App\Models\FolderHasModule;
use App\Models\Module;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FolderController extends Controller
{
    public function index($id) {
        try {
            $user = Auth::user();
            $folder = Folder::find($id);
            $folder_user_id = $folder->user_id;
            if ($user->id == $folder_user_id) {
                return response()->json($folder, 200);
            }
            else {
                return response()->json([
                    "message" => 'Can not find folder'
                ]);
            }
        }
        catch (\Exception $exception) {
            return response()->json([
                "message" => 'Can not find folder'
            ]);
        }
    }
    public function listFolders() {
        $user = Auth::user();
        if ($user) {
            $user_id = $user->id;
            $folders = User::find($user_id)->folders;
            return response()->json($folders, 200);
        }
        else {
            return response()->json([
                'message' => 'Not logged in, failed !'
            ], 500);
        }
    }
    public function create(Request $request) {
        $this->validate(
            $request,
            [
                'name' => 'required | string',
                'public' => 'required | integer',
                'description' => 'string | nullable'
            ]
        );
        $user = Auth::user();
        if ($user) {
            $current_time = getCurrentTime();
            $code = Str::random(25);
            $folder_data = [
                'name' => htmlspecialchars($request->name),
                'public' => (int) $request->public,
                'description' => htmlspecialchars($request->description),
                'created_at' => $current_time,
                'updated_at' => $current_time,
                'user_id' => $user->id,
                'code' => $code
            ];
            try {
                $folder = Folder::create($folder_data);
                return response()->json($folder, 200);
            }
            catch (\Exception $exception) {
                return response()->json([
                    "message" => $exception->getMessage()
                ], 500);
            }
        }
        else {
            return response()->json([
                "message" => 'Create folder failed'
            ], 500);
        }
    }
    public function update($folder_id, Request $request) {
        $this->validate(
            $request,
            [
                'name' => 'string | nullable',
                'public' => 'integer | nullable',
                'description' => 'string | nullable'
            ]
        );
        $folder = Folder::find($folder_id);
        if ($folder) {
            $update_data = [
                'name' => isset($request->name) ? htmlspecialchars($request->name) : $folder->name,
                'public' => isset($request->public) ? (int) $request->public : $folder->public,
                'description' => htmlspecialchars($request->description)
            ];
            try {
                $folder->update($update_data);
                return $this->index($folder_id);
            }
            catch (\Exception $exception) {
                return response()->json([
                    'message' => 'Update folder failed'
                ], 500);
            }
        }
        else {
            return response()->json([
                'message' => 'Folder not found'
            ], 500);
        }
    }
    public function delete($id) {
        try {
            Folder::find($id)->delete();
            return $this->listFolders();
        }
        catch (\Exception $exception) {
            return response()->json([
                'message' => 'Can not delete folder'
            ]);
        }
    }
    public function modules(Request $request) {
        $user = Auth::user();
        $req_folder_id = $request->query('folder_id');
        if ($req_folder_id) {
            $folder = Folder::find($req_folder_id);
            $folder_user_id = $folder->user->id;
            if ($folder_user_id == $user->id) {
                try {
                    $module = DB::table('module')
                        ->join('folder_has_module', 'module_id', '=', 'folder_has_module.module_id')
                        ->where('folder_has_module.folder_id', '=', $req_folder_id)
                        ->select('module.*')
                        ->get();
                    return response()->json($module, 200);
                }
                catch (\Exception $exception) {
                    return response()->json([
                        'message' => 'Get modules by folder failed'
                    ], 500);
                }
            }
            else {
                return response()->json([
                    'message' => 'Get modules by folder failed'
                ], 500);
            }
        }
    }
    public function assignModule($module_id, $folder_id) {
        $user = Auth::user();
        $module = Module::find($module_id);
        $folder = Folder::find($folder_id);
        if ($user->id == $module->user->id && $user->id == $folder->user->id) {
            $current_time = getCurrentTime();
            $data = [
                'folder_id' => $folder_id,
                'module_id' => $module_id,
                'created_at' => $current_time,
                'updated_at' => $current_time,
            ];
            try {
                $instance = FolderHasModule::create($data);
                return response()->json($instance, 200);
            }
            catch (\Exception $exception) {
                return response()->json([
                    "message" => "Assign module failed"
                ], 500);
            }
        }
        else {
            return response()->json([
                'message' => 'Module not found'
            ]);
        }
    }
    public function deleteModuleFromFolder(Request $request) {
        $module_id = $request->query('module_id');
        $folder_id = $request->query('folder_id');
        try {
            FolderHasModule::where('module_id', '=', $module_id)
                ->where('folder_id', '=', $folder_id)
                ->delete();
            return $this->modules($request);
        }
        catch (\Exception $exception) {
            return response()->json([
                'message' => 'Delete module failed'
            ], 500);
        }
    }
}
