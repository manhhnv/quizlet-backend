<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
public function signup(Request $request) {
        $this->validate(
            $request,
            [
                'username' => 'required | string | unique:users',
                'email' => 'required | string | email | unique:users',
                'password' => 'required|min:6|regex:/^.*(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])/',
            ],
            [
                'username.required' => 'Username fill can not be blank!',
                'email.required' => 'Email fill can not be blank!',
                'password.required' => 'Password fill can not be blank!',
                'password.min' => 'Password contain at least 6 characters, include uppercase, lowercase and number',
                'password.regex' => 'Password must be included uppercase, lowercase and number'
            ]
        );
        if ($request->hasFile('avatar')) {
            $file = $request->avatar;
            $ext = '.' . $file->getClientOriginalExtension();
            $avatar = (int) getLastUserId() + 1 . $ext;
            $path = 'users/images/avatars';
            $file->move(base_path('public/' . $path), $avatar);
            $avatarPath = url($path) . '/' . $avatar;
        }
        $currentTime = getCurrentTime();
        $random = mt_rand(100000, 999999);
        $username = htmlspecialchars($request->username);
        $email = htmlspecialchars($request->email);
        $user_data = [
            'username' => $username,
            'email' => $email,
            'password' => bcrypt($request->password),
            'avatar' => isset($avatarPath) ? $avatarPath : null,
            'verified' => false,
            'verify_code' => $random,
            'created_at' => $currentTime,
            'updated_at' => $currentTime,
        ];
        try {
            $user = User::create($user_data);
            return response()->json($user, 200);
        }
        catch (\Exception $exception) {
            return response()->json($exception->getMessage(), 500);
        }
    }

    public function login(Request $request) {
        $this->validate(
            $request,
            [
                'email' => 'required|string|email',
                'password' => 'required|string',
                'remember_me' => 'boolean'
            ],
            [
                'email.required' => 'Email can not be blank !',
                'password.required' => 'Password can not be blank !',
            ]
        );
        $credentials = request(['email', 'password']);
        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }
        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        if ($request->remember_me)
            $token->expires_at = Carbon::now()->addWeeks(1);
        $token->save();
        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString()
        ]);
    }
    public function logout(Request $request) {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }
    public function user()
    {
        return response()->json(Auth::user());
    }
}
