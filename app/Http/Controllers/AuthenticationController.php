<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * This JWT auth implementation is based of "JWT User Authentication with Lumen API" by Ben Lobaugh
 * @see https://benlobaugh.medium.com/jwt-user-authentication-with-lumen-api-ee3b8e69c678
 * @see https://github.com/blobaugh/lumen-api-jwt-auth-example
 */
class AuthenticationController extends Controller
{
    /**
     * Register/creates a new user
     *
     * @param Request $request
     * @return Response
     */
    public function register(Request $request)
    {
        $this->validate($request, [
            'name'     => 'required|string|between:2,100',
            'email'    => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:6',
        ]);

        try {
            $user = new User([
                'name' => $request->name,
                'email' => $request->email,
            ]);
            $user->password = app('hash')->make($request->password);
            $user->save();
            return response()->json(['user' => $user->only(['name', 'email']), 'message' => 'User Registered'], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'User Registration Failed'], 500);
        }
    }

    /**
     * Attempt to authenticate the user and retrieve a JWT.
     *
     * @param Request $request
     * @return Response
     */
    public function login(Request $request)
    {
        $this->validate($request, [
            'email'    => 'required|string',
            'password' => 'required|string',
        ]);
        $credentials = $request->only(['email', 'password']);
        if (! $token = Auth::attempt($credentials)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        return $this->respondWithToken($token);
    }

    /**
     * Log the user out (Invalidate the token). Requires a login to use as the
     * JWT in the Authorization header is what is invalidated
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout() {
        auth()->logout();
        return response()->json(['message' => 'User successfully signed out']);
    }

    /**
     * Refresh the current token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh() {
        return $this->respondWithToken( Auth::refresh() );
    }

    /**
     * Helper function to format the response with the token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    private function respondWithToken($token)
    {
        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60
        ], 200);
    }
}