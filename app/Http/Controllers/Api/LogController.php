<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Log;
use App\Models\User;
use Illuminate\Http\Response;

class LogController extends Controller
{
    public function index(User $user)
    {
        if (empty($user)) {
            return responseFailed('User not found', Response::HTTP_NOT_FOUND);
        }
        $data = Log::query()->where('user_id', $user->id)->orderBy('id', 'desc')->paginate(10);
        return responseSuccess($data);
    }
}