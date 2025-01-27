<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Http\Requests\LoginRequest;
use App\Traits\ApiJsonResponsesTrait;
use Hash;

class LoginController extends Controller
{
    use ApiJsonResponsesTrait;

    public function login(LoginRequest $request)
    {
        $request->validated();

        $user = User::firstWhere('username', $request->username);

        if($user && Hash::check($request->password, $user->password)) {
            return $this->success('Authorized.', [
                'token' => $user->createToken($request->username, ["*"], now()->addHour())->plainTextToken
            ], 200);
        }

        return $this->error('Unauthorized.', 401);
    }
}
