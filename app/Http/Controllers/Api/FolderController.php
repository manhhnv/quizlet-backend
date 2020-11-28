<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\ShareLinkQuizlet;
use App\Models\Folder;
use App\Models\FolderHasModule;
use App\Models\Module;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;

class FolderController extends Controller
{
    private $module_service;
    public function __construct() {
        $this->module_service = new ModuleController();
    }

    public function folderDetail($folder_id) {
        try {
            $folder = Folder::find($folder_id);
            $user = Auth::user();
            $folder_user_id = $folder->user->id;
            if ($user->id == $folder_user_id) {
                return response()->json($folder, 200);
            }
            else {
                return response()->json([
                    "error" => 'Can not find folder'
                ], 400);
            }
        }
        catch (\Exception $exception) {
            return response()->json([
                "message" => $exception->getMessage()
            ], 500);
        }
    }
    public function index(Request $request) {
        try {
            $id = (int) $request->query('id');
            $code = $request->query('code');
            $user = Auth::user();
            $folder = Folder::find($id);
            $folder_user_id = $folder->user_id;

            if ($folder->code == $code) {
                if ($folder->public == 1) {
                    return response()->json($folder, 200);
                }
                else {
                    if ($folder_user_id == $user->id) {
                        return response()->json($folder, 200);
                    }
                    else return response()->json([
                        "message" => "Can not access this folder"
                    ], 400);
                }
            }
            else {
                return response()->json([
                    "message" => 'Can not find folder'
                ], 400);
            }
        }
        catch (\Exception $exception) {
            return response()->json([
                "message" => $exception->getMessage()
            ], 400);
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
            $current_time = getCurrentTime();
            $update_data = [
                'name' => isset($request->name) ? htmlspecialchars($request->name) : $folder->name,
                'public' => isset($request->public) ? (int) $request->public : $folder->public,
                'description' => htmlspecialchars($request->description),
                'updated_at' => $current_time
            ];
            try {
                $folder->update($update_data);
                return $this->folderDetail($folder_id);
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
        $folder_id = $request->query('folder_id');
        if ($folder_id) {
            return $this->module_service->modulesInFolderService($folder_id);
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
                return $this->module_service->modulesInFolderService($folder_id);
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
            ], 400);
        }
    }
    public function deleteModuleFromFolder(Request $request) {
        $module_id = $request->query('module_id');
        $folder_id = $request->query('folder_id');
        $user = Auth::user();
        $folder = Folder::find($folder_id);
        if ($user->id == $folder->user->id) {
            try {
                FolderHasModule::where('module_id', '=', $module_id)
                    ->where('folder_id', '=', $folder_id)
                    ->delete();
                return $this->module_service->modulesInFolderService($folder_id);
            }
            catch (\Exception $exception) {
                return response()->json([
                    'message' => $exception->getMessage()
                ], 500);
            }
        }
        else {
            return response()->json([
                'message' => 'Delete module failed'
            ], 500);
        }
    }
    public function addModuleInFolder($id, $code, Request $request) {
        $user = Auth::user();
        $folder = Folder::find($id);
        $folder_user_id = $folder->user->id;
        if ($user && $user->id == $folder_user_id && $folder->code == $code) {
            try {
                $module = $this->module_service->create($request)->original;
                if ($module) {
                    $current_time = getCurrentTime();
                    $data = [
                        'folder_id' => $id,
                        'module_id' => $module->id,
                        'created_at' => $current_time,
                        'updated_at' => $current_time
                    ];
                    $instance = FolderHasModule::create($data);
                    return $this->module_service->modulesInFolderService($id);
                }
                else {
                    return response()->json([
                        "message" => 'Can not create module'
                    ]);
                }
            }
            catch (\Exception $exception) {
                return response()->json([
                    "message" => $exception->getMessage()
                ], 500);
            }
        }
    }
    public function sharing($username, $folder_id, $code) {
        $user = Auth::user();
        $folder = Folder::find($folder_id);
        $folder_user_id = $folder->user->id;
        $shared_user = User::where('username', $username)->first();
        if ($shared_user->id == $folder_user_id && $folder->code == $code) {
            if ($user->id == $folder_user_id) {
                return Redirect::to('localhost:3000/folder?code' . $code . '&id=' . $folder_id);
            }
            else {
                if ($folder->public == 1) {

                }
            }
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
    public function generateLink($id, $code) {
        try {
            $folder = Folder::find($id);
            if ($folder->code == $code) {
                $folder_user_id = $folder->user->id;
                $owner = User::find($folder_user_id);
                $shared_link = 'http://localhost:3000/' . $owner->username . '/folder?' . 'code=' . $code . '&id=' . $id;
//                $this->dispatch(new ShareLinkQuizlet($owner->email, $to_address, $shared_link));
                return response()->json([
                    "link" => $shared_link
                ], 200);
            }
            else {
                return response()->json([
                    "message" => "Can not find folder"
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
