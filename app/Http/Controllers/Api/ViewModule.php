<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ViewModule extends Controller
{
    public function viewModule($id) {
        $user = Auth::user();
        $module = Module::find($id);
        $user = User::find($user->id);
        if ($user->can('view', $module)) {
            return response()->json($module, 200);
        }
        else {
            return response()->json([
                "message" => "Can not view this module"
            ]);
        }
    }
}
