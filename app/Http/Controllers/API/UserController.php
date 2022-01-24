<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    // auth user data
    public function user(Request $request) {
        return new UserResource($request->user());
    }

    public function update(Request $request) {
        return ;
    }

    public function updatePassword(Request $request) {
        $credentials = $this->validate($request, [
            'password' => ['required', 'string', Password::min(6), 'confirmed'],
        ]);

        $user = $request->user();

        $user->update([
            'password'  => Hash::make($credentials['password']),
        ]);

        return response()->json($user);
    }
}
