<?php

namespace App\Domain\Auth\Actions;

use App\Models\User;
use App\Models\UserLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterUser
{
    public function execute(array $data, Request $request): User
    {
        $user = new User;
        $user->name = $data['name'] ?? null;
        $user->email = $data['email'];
        $user->password = Hash::make($data['password']);
        $user->mobile = $data['mobile'] ?? null;
        $user->mobile_country_code = $data['mobile_country_code'] ?? null;
        $user->status = 'active';
        $user->save();

        $this->logLogin($user, $request);

        return $user;
    }

    private function logLogin(User $user, Request $request): void
    {
        $login = new UserLogin;
        $login->user_id = $user->id;
        $login->ip_address = $request->ip();
        $login->user_agent = $request->userAgent();
        $login->logged_in_at = now();
        $login->save();
    }
}
