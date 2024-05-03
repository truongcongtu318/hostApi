<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Hash;

class ApiController extends Controller
{
    //POST[name, email, password]
    public function register(Request $request)
    {
        //validate
        $request->validate([
            'name' => "required|string",
            'email' => "required|email|string|unique:users",
            'password' => "required|confirmed",
        ]);

        //create user
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return response()->json([
            "status" => true,
            "message" => "User registered successfully",
            "data" => [$request->data]
        ]);

    }
    //GET[email,password]
    public function login(Request $request)
    {
        //validate
        $request->validate([
            'email' => "required|email|string",
            'password' => "required"
        ]);
        //check user
        $user = User::where('email', $request->email)->first();
        if(!empty($user)){
            if(Hash::check($request->password, $user->password)){
                $token = $user->createToken('myToken')->plainTextToken;
                return response()->json([
                    "status" => true,
                    "message" => "User logged in successfully",
                    "token" => $token,
                    "data" => [$user]
                ]);
            }else{
                return response()->json([
                    "status" => false,
                    "message" => "Invalid password",
                    "data" => []
                ]);
            }
        }else{
            return response()->json([
                'status' => false,
                'message' => "Not contains user"
            ]);
        }

    }

    public function profile()
    {
        $user_data = auth()->user();

        return response()->json([
            "status" => true,
            "message" => "User profile",
            "data" => $user_data
        ]);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        return response()->json([
            "status" => true,
            "message" => "User logged out successfully",
            "data" => []
        ]);
    }
}