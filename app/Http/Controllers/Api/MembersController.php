<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\MembersHasClasses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MembersController extends Controller
{
    public function listMembers($class_id) {
        try {
            $members = DB::table('members')
                ->join('members_has_classes', 'members_has_classes.member_id', '=', 'members.id')
                ->where('members_has_classes.class_id', '=', $class_id)
                ->select('members.*')
                ->get();
            return response()->json($members, 200);
        }
        catch (\Exception $exception) {
            return response()->json([
                "message" => 'Can not get list members'
            ], 500);
        }
    }
    public function joinedClass() {
        $user = Auth::user();
        try {
            $classes = DB::table('class')
                ->join('members_has_classes', 'members_has_classes.class_id', '=', 'class.id')
                ->where('members_has_classes.member_id', '=', $user->id)
                ->select('class.*')
                ->get();
//            $classes = MembersHasClasses::where('member_id', '=', $user->id)
//                ->get();
            return response()->json($classes, 200);
        }
        catch (\Exception $exception) {
            return response()->json([
                "message" => 'Get joined class failed'
            ], 500);
        }
    }
    public function join($class_id, Request $request) {
        $code = $request->query('code');
        $class = ClassModel::find($class_id);
        $user = Auth::user();
        if ($class->code == $code && $class->public) {
            $current_time = getCurrentTime();
            $data = [
                'member_id' => $user->id,
                'class_id' => $class_id,
                'created_at' => $current_time,
                'updated_at' => $current_time
            ];
            try {
                $instance = MembersHasClasses::create($data);
                return $this->joinedClass();
            }
            catch (\Exception $exception) {
                return response()->json([
                    'message' => 'Can not join this class'
                ], 500);
            }
        }
        else {
            return response()->json([
                'message' => 'Can not join this class'
            ], 500);
        }
    }
    public function leaveClass($class_id) {
        $user = Auth::user();
        try {
            MembersHasClasses::where('member_id', '=', $user->id)
                ->where('class_id', '=', $class_id)
                ->delete();
            return $this->joinedClass();
        }
        catch (\Exception $exception) {
            return response()->json([
                "message" => 'You not join this class'
            ], 500);
        }
    }
    public function deleteMemberFromClass(Request $request) {
        $class_id = $request->query('class_id');
        $member_id = $request->query('member_id');
        if ($class_id && $member_id) {
            try {
                MembersHasClasses::where('class_id', '=', $class_id)
                    ->where('member_id', '=', $member_id)
                    ->delete();
                return $this->listMembers($class_id);
            }
            catch (\Exception $exception) {
                return response()->json([
                    "message" => 'Delete failed'
                ], 500);
            }
        }
        else {
            return response()->json([
                "message" => 'You must provide class and member !'
            ]);
        }

    }
}
