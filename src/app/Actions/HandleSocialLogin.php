<?php

namespace App\Actions;

use App\Models\SocialAccount;
use App\Models\User;
use App\Models\UserLogin;
use Illuminate\Http\Request;
use Laravel\Socialite\Contracts\User as SocialUser;

class HandleSocialLogin
{
    public function execute(string $provider, SocialUser $socialUser, Request $request): User
    {
        $account = SocialAccount::where('provider', $provider)
            ->where('provider_id', $socialUser->getId())
            ->first();

        if ($account) {
            $account->token = $socialUser->token;
            $account->refresh_token = $socialUser->refreshToken;
            $account->token_expires_at = $socialUser->expiresIn ? now()->addSeconds($socialUser->expiresIn) : null;
            $account->save();

            $user = $account->user;
        } else {
            $user = User::where('email', $socialUser->getEmail())->first();

            if (! $user) {
                $user = new User;
                $user->name = $socialUser->getName();
                $user->email = $socialUser->getEmail();
                $user->email_verified_at = now();
                $user->avatar_path = $socialUser->getAvatar();
                $user->status = 'active';
                $user->save();
            }

            $account = new SocialAccount;
            $account->user_id = $user->id;
            $account->provider = $provider;
            $account->provider_id = $socialUser->getId();
            $account->token = $socialUser->token;
            $account->refresh_token = $socialUser->refreshToken;
            $account->token_expires_at = $socialUser->expiresIn ? now()->addSeconds($socialUser->expiresIn) : null;
            $account->save();
        }

        $login = new UserLogin;
        $login->user_id = $user->id;
        $login->ip_address = $request->ip();
        $login->user_agent = $request->userAgent();
        $login->logged_in_at = now();
        $login->save();

        return $user;
    }
}
