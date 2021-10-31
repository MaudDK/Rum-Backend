<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request){
        $fields = $request->validate([
            'name' => 'required|string',
            'username' => 'required|string|unique:users,username',
            // 'regkey' => 'required|string',
            'password' => 'required|string|confirmed',
        ]);

        $user = User::create([
            'name' => $fields['name'],
            'username' => $fields['username'],
            'password' => bcrypt($fields['password'])
        ]);

        $token = $user->createToken('apptoken')->plainTextToken;
        
        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }

    public function login(Request $request){
        $fields = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        //Check username
        $user = User::where('username', $fields['username'])->first();

        //Check password
        if(!$user || !Hash::check($fields['password'], $user->password)){
            return response([
                'message' => "Incorrect Username or Password"
            ], 401);
        }
        $user->tokens()->delete();
        $token = $user->createToken('apptoken')->plainTextToken;
        
        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }

    public function logout(Request $request){
        auth()->user()->tokens()->delete();
        
        return [
            'message'=> 'Logged out'
        ];
    }
}
