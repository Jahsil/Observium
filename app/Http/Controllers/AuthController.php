<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Cookie;
use Auth;

class AuthController extends Controller
{
    //
    public function signUp(Request $request){
        $email = $request->email;
        $user = User::where("email",$email)->first();
        // dd($user);
        if($user){
            $response = [
                "status" => "Failed",
                "message" => "User already exists"
            ];
            return response()->json($response , 404);
        }

        $validated_data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:3'
        ]);

        $new_user = User::create($validated_data);

        $response = [
            "status" => "OK",
            "message" => "User created successfully",
            "data" => $new_user
        ];

        return response()->json($response , 200);
    }

    public function signIn(Request $request){
        $email = $request->email;
        $user = User::where("email",$email)->first();
        // dd($user);
        if($user == null){
            $response = [
                "status" => "Failed",
                "message" => "User doesnot exist"
            ];
            return response()->json($response , 404);
        }

        $credentials = $request->only('email', 'password');

        $token = JWTAuth::claims([
            "username" => $request->input('username'),
            "role" => "Engineering" 
        ])->attempt($credentials);
        
        
        $cookie = Cookie::make('token', $token, 60);
        $user = Auth::user();
        Auth::login($user);

        $response = [
            "status" => "OK",
            "message" => "User returned",
            "data" => $user,
            "token" => $token 
        ];
        return response()->json($response , 200)->withCookie(cookie('token', $token, 60));
        

    }

    public function usersList(Request $request){
        try {
            $users = User::all();
            return response()->json([
                'message' => 'Users List',
                'users' => $users,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'No users'], 500);
        }
    }

    public function signOut(Request $request){

        if (auth()->check()) {
            try {
                JWTAuth::invalidate($request->bearerToken());
                return response()->json(['message' => 'Successfully logged out']);
            } catch (\Exception $e) {
                return response()->json(['error' => 'Failed to logout'], 500);
            }
        } else {
            return response()->json(['error' => 'User is not logged in'], 401);
        }
        
    }

    
}
