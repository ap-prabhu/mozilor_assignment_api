<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'user_name' => 'required|string|max:50',
            'mobile_number'=> 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([ 'status'=>0, 'error' => $validator->errors()], 400);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'user_name' => $request->user_name,
            'mobile_number' => $request->mobile_number,
        ]);
        
        return response()->json([
            'status'=>1,
            'message' => "Registration Completed Successfully!",
        ], 201);
    }

    public function login(Request $request)
    {
       
        $validator = Validator::make($request->all(), [
            'email' => 'required|string',
            // 'username_or_email' => 'required|string',
            'password' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json([ 
                'status'=>0, 
                'error' => $validator->errors()
            ], 400);
        }

        // $user = User::where('email', $request->username_or_email)
        //         ->orWhere('user_name', $request->username_or_email)
        //         ->first();
        // if (!$user || !Hash::check($request->password, $user->password)) {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['status' => 0,'error' => 'Invalid email or password. Please try again.'], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status'=>1,
            'message'=>'Logged In Successfully!',
            'token_type' => 'Bearer',
            'access_token' => $token,
            'user_deatils' => $user
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([ 'status'=>1, 'message' => 'Logged Out successfully!.']);
    }
}
