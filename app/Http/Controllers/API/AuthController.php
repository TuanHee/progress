<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $credentials = $this->validate($request, [
            'name' => ['required', 'string'],
            'email' => ['required', 'string', 'email', 'unique:users'],
            'password' => ['required', 'string', Password::min(6), 'confirmed'],
            'device_name' => ['required'],
        ]);

        $user = User::create([
            'name'  => $credentials['name'],
            'email' => $credentials['email'],
            'password'  => Hash::make($credentials['password']),
        ]);

        return response()->json([
            'user'  => $user,
            'token'  => $user->createToken($request->get('device_name'))->plainTextToken,
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $this->validate($request, [
            'email'         => ['required', 'email'],
            'password'      => ['required', Password::min(6)],
            'device_name'   => ['required'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.']
            ]);
        }

        return response()->json([
            'user'  => $user,
            'token' => $user->createToken($request->device_name)->plainTextToken,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out.'
        ]);
    }
}
