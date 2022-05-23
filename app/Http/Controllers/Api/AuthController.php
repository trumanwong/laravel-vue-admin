<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * Class AuthController
 *
 * @package App\Http\Controllers\Api
 */
class AuthController extends BaseController
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        $user = User::query()->where('email', $request->input('email'))->first();
        if (empty($user) || !Hash::check($request->input('password'), $user->password)) {
            return responseFailed('These credentials do not match our records.', Response::HTTP_UNAUTHORIZED);
        }

        $user->token = $user->createToken('laravel-vue-admin')->plainTextToken;

        return responseSuccess($user);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        Auth::guard('web')->logout();
        return responseSuccess();
    }

    public function user(Request $request): UserResource
    {
        return new UserResource($request->user());
    }
}
