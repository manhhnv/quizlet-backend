<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            $class_data = [
                'name' => htmlspecialchars($request->name),
                'public' => (int) $request->public,
                'user_id' => $user['id'],
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
}
