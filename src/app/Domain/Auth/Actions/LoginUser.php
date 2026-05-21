<?php

namespace App\Domain\Auth\Actions;

use App\Models\User;
use App\Models\UserLogin;
use Illuminate\Http\Request;

class LoginUser
{
    public function logLogin(User $user, Request $request): void
    {
        $login = new UserLogin;
        $login->user_id = $user->id;
        $login->ip_address = $request->ip();
        $login->user_agent = $request->userAgent();
        $login->logged_in_at = now();
        $login->save();
    }
}
