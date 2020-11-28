<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\ShareLinkQuizlet;
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

    private $module_service;
    private $folder_service;
    public function __construct() {
        $this->module_service = new ModuleController();
        $this->folder_service = new FolderController();
    }

    public function index(Request $request) {
        try {
            $id = (int) $request->query('id');
            $code = $request->query('code');
            $user = Auth::user();
            $class = ClassModel::find($id);
            if ($class->code == $code) {
                if ($class->public == 1) {
                    return response()->json($class, 200);
                }
                else {
                    if ($class->user->id == $user->id) {
                        return response()->json($class, 200);
                    }
                    else return response()->json([
                        "message" => "Can not access this class"
                    ], 400);
                }
            }
            else {
                return response()->json([
                    "message" => 'Can not find class'
                ], 400);
            }
        }
        catch (\Exception $exception) {
            return response()->json([
                "message" => $exception->getMessage()
            ], 500);
        }
    }
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
                'description' => 'string'
            ],
            [
                'name.required' => 'Class name can not be blank !',
            ]
        );
        if ($user) {
            $current_time = getCurrentTime();
            $code = Str::random(40);
            $class_data = [
                'name' => htmlspecialchars($request->name),
                'public' => (int) $request->public,
                'user_id' => $user['id'],
                'code' => $code,
                'description' => htmlspecialchars($request->description),
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

        $this->validate(
            $request,
            [
                'name' => 'string | nullable',
                'public' => 'integer | nullable',
                'description' => 'string | nullable'
            ]
        );
        $user = Auth::user();
        $class = ClassModel::find($id);
        if ($class && $class->user->id == $user->id) {
            $current_time = getCurrentTime();
            $update_data = [
                'name' => isset($request->name) ? htmlspecialchars($request->name) : $class->name,
                'public' => isset($request->public) ? (int) $request->public : $class->public,
                'description' => isset($request->description) ? htmlspecialchars($request->description): $class->description,
                'updated_at' => $current_time
            ];
            try {
                $class->update($update_data);
                return response()->json(ClassModel::find($id), 200);
            }
            catch (\Exception $exception) {
                return response()->json([
                    "message" => $exception->getMessage()
                ], 500);
            }
        }
        else {
            return response()->json([
                "message" => "Can not find class"
            ], 400);
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
            return $this->all();
        }
        catch (\Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage()
            ], 500);
        }
    }

    public function modules(Request $request) {
        $req_class_id = (int) $request->query('class_id');
        if ($req_class_id != null) {
            return $this->module_service->modulesInClassService($req_class_id);
        }
        else {
            return response()->json(
                ["message" => 'Error'], 500
            );
        }
    }
    public function addModuleInClass($id, $code, Request $request) {
        $class = ClassModel::find($id);
        $class_user_id = $class->user->id;
        $user = Auth::user();
        if ($class_user_id == $user->id && $class->code == $code) {
            try {
                $module = $this->module_service->create($request)->original;
                if ($module != null) {
                    $current_time = getCurrentTime();
                    $data = [
                        'module_id' => $module->id,
                        'class_id' => $id,
                        'created_at' => $current_time,
                        'updated_at' => $current_time
                    ];
                    $instance = ClassHasModule::create($data);
                    return $this->module_service->modulesInClassService($id);
                }
                else {
                    return response()->json([
                        "message" => "Create module failed"
                    ], 400);
                }
            }
            catch (\Exception $exception) {
                return response()->json([
                    "message" => $exception->getMessage()
                ], 500);
            }
        }
        else {
            return response()->json([
                "message" => 'Authorized'
            ], 401);
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
                    return $this->module_service->modulesInClassService($class_id);
                }
                catch (\Exception $exception) {
                    return response([
                        'message' => $exception->getMessage()
                    ], 500);
                }
            }
        }
        else {
            return response()->json([
                'message' => 'Authorization'
            ], 401);
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
                    return $this->module_service->modulesInClassService($req_class_id);
                }
                catch (\Exception $exception) {
                    return response()->json(['message' => $exception->getMessage()], 500);
                }
            }
        }
        else {
            return response()->json([
                'message' => 'Delete module failed'
            ], 400);
        }
    }
    public function folders(Request $request) {
        $class_id = $request->query('class_id');
        if ($class_id != null) {
            return $this->folder_service->foldersInClassService($class_id);
        }
        else {
            return response()->json([
                "message" => "Error"
            ], 500);
        }
    }

    public function addFolderToClass($id, $code, Request $request) {
        $class = ClassModel::find($id);
        $class_user_id = $class->user->id;
        $user = Auth::user();
        if ($class_user_id == $user->id && $class->code == $code) {
            try {
                $folder = $this->folder_service->create($request)->original;
                if ($folder != null) {
                    $current_time = getCurrentTime();
                    $data = [
                        'folder_id' => $folder->id,
                        'class_id' => $id,
                        'created_at' => $current_time,
                        'updated_at' => $current_time
                    ];
                    $instance = ClassHasFolder::create($data);
                    return $this->folder_service->foldersInClassService($id);
                }
                else {
                    return response()->json([
                        "message" => "Create folder failed"
                    ], 400);
                }
            }
            catch (\Exception $exception) {
                return response()->json([
                    "message" => $exception->getMessage()
                ], 500);
            }
        }
    }
    public function assignFolder($folder_id, $class_id) {
        $user = Auth::user();
        if ($user != null) {
            $folder = Folder::find($folder_id);
            $class = ClassModel::find($class_id);
            if ($user->id == $folder->user->id && $user->id == $class->user->id) {
                $current_time = getCurrentTime();
                $data = [
                    'folder_id' => (int) $folder_id,
                    'class_id' => (int) $class_id,
                    'created_at' => $current_time,
                    'updated_at' => $current_time,
                ];
                try {
                    $instance = ClassHasFolder::create($data);
                    return $this->folder_service->foldersInClassService($class_id);
                }
                catch (\Exception $exception) {
                    return response()->json([
                        "message" => $exception->getMessage()
                    ], 500);
                }
            }
            else {
                return response()->json([
                    "message" => 'You can not edit class'
                ], 400);
            }
        }
        else {
            return response()->json([
                "message" => 'You can not edit class'
            ], 400);
        }
    }
    public function deleteFolder(Request $request) {
        $user = Auth::user();
        $folder_id = $request->query('folder_id');
        $class_id = $request->query('class_id');
        if ($folder_id && $class_id) {
            $class = ClassModel::find($class_id);
            if ($user->id == $class->user->id) {
                try {
                    ClassHasFolder::where('class_id', '=', $class_id)
                        ->where('folder_id', '=', $folder_id)
                        ->delete();
                    return $this->folder_service->foldersInClassService($class_id);
                }
                catch (\Exception $exception) {
                    return response()->json([
                        "message" => $exception->getMessage()
                    ], 500);
                }
            }
            else {
                return response()->json([
                    'message' => 'Delete folder failed'
                ], 400);
            }
        }
        else {
            return response()->json([
                'message' => 'Delete folder failed'
            ], 400);
        }
    }

    public function generateLink($id, $code) {
        try {
            $class = ClassModel::find($id);
            if ($class->code == $code) {
                $class_user_id = $class->user->id;
                $owner = User::find($class_user_id);
                $shared_link = 'http://localhost:3000/' . $owner->username . '/class?' . 'code=' . $code . '&id=' . $id;
                return response()->json([
                    "link" => $shared_link
                ], 200);
            }
            else {
                return response()->json([
                    "message" => "Can not find class"
                ], 400);
            }
        }
        catch (\Exception $exception) {
            return response()->json([
                "message" => $exception->getMessage()
            ], 500);
        }
    }
    public function sendSharedLink(Request $request) {
        $this->validate(
            $request,
            [
                'from' => 'required|email',
                'to' => 'required | email',
                'link' => 'required | string'
            ],
            [
                'from.email' => 'From address is email format',
                'to.email' => 'To address is email format',
                'link.required' => 'Link can not be blank'
            ]
        );
        $from = $request->from;
        $to = $request->to;
        $link = $request->link;
        $this->dispatch(new ShareLinkQuizlet($from, $to, $link));
    }
}
