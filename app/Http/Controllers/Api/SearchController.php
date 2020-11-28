<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    private $key_search;
    private $type_sort;
    public function __construct() {
        $this->key_search = array('name', 'created_at', 'username');
        $this->type_sort = ['desc', 'asc'];
    }

    public function searchModule(Request $request){
        $name = $request->query('name');
        $order = $request->query('order');
        $type_sort = $request->query('type_sort');

        if (!in_array($order, $this->key_search) || $order == 'username') {
            $order = 'name';
        }
        if (!in_array($type_sort, $this->type_sort)) {
            $type_sort = 'asc';
        }
        $user = Auth::user();
        if ($user != null) {
            try {
                $module = DB::table('module')
                    ->where('name', 'like', '%'.$name . '%')
                    ->where('public', '<>', 0)
                    ->orWhere(function ($query) use($user, $name) {
                        $query->where('user_id', '=', $user->id)
                            ->where('name', 'like', '%'.$name . '%');
                    })
                    ->orderBy($order, $type_sort)
                    ->paginate(10);
                return response()->json($module, 200);
            }
            catch (\Exception $exception) {
                return response()->json([
                    "message" => $exception->getMessage()
                ], 500);
            }
        }
        else {
            return response()->json([
                "message" => "Error"
            ], 500);
        }
    }
    public function searchFolder(Request $request) {
        $name = $request->query('name');
        $order = $request->query('order');
        $type_sort = $request->query('type_sort');
        if (!in_array($order, $this->key_search) || $order == 'username') {
            $order = 'name';
        }
        if (!in_array($type_sort, $this->type_sort)) {
            $type_sort = 'asc';
        }
        $user = Auth::user();
        try {
            $folders = DB::table('folder')
                ->where('name', 'like', '%'.$name.'%')
                ->where('public', '<>', 0)
                ->orWhere(function ($query) use ($user, $name) {
                    $query->where('user_id', '=', $user->id)
                        ->where('name', 'like', $name);
                })
                ->orderBy($order, $type_sort)
                ->paginate(10);
            return response()->json($folders, 200);
        }
        catch (\Exception $exception) {
            return response()->json([
                "message" => $exception->getMessage()
            ], 500);
        }
    }
    public function searchClass(Request $request) {
        $name = $request->query('name');
        $order = $request->query('order');
        $type_sort = $request->query('type_sort');

        if (!in_array($order, $this->key_search) || $order == 'username') {
            $order = 'name';
        }
        if (!in_array($type_sort, $this->type_sort)) {
            $type_sort = 'asc';
        }
        $user = Auth::user();
        try {
            $classes = DB::table('class')
                ->where('name', 'like', '%'.$name.'%')
                ->where('public', '<>', 0)
                ->orWhere(function ($query) use ($user, $name) {
                   $query->where('user_id', '=', $user->id)
                       ->where('name', 'like', '%'.$name.'%');
                })
                ->orderBy($order, $type_sort)
                ->paginate(10);
            return response()->json($classes, 200);
        }
        catch (\Exception $exception) {
            return response()->json([
                "message" => $exception->getMessage()
            ], 500);
        }
    }
    public function searchUser(Request $request) {
        $username = $request->query('username');
        $order = $request->query('order');
        $type_sort = $request->query('type_sort');
        if (!in_array($order, $this->key_search) || $order == 'name') {
            $order = 'username';
        }
        if (!in_array($type_sort, $this->type_sort)) {
            $type_sort = 'asc';
        }
        if (Auth::user()) {
            try {
                $users = DB::table('users')
                    ->where('verified', '<>', 0)
                    ->where('username', 'like', $username)
                    ->orderBy($order, $type_sort)
                    ->paginate(10);
                return response()->json($users, 200);
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
}
