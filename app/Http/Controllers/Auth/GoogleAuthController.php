<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
{
    $googleUser = Socialite::driver('google')->user();

    $user = User::updateOrCreate(
        ['email' => $googleUser->getEmail()],
        [
            'name' => $googleUser->getName(),
            'google_id' => $googleUser->getId(),
            'password' => bcrypt(str()->random(24)),
        ]
    );

    Auth::login($user);

    $token = $user->createToken('parrotcare-web')->plainTextToken;

    $frontendUrl = 'http://localhost:3000'; // 之後正式上線要換成你的 Vercel 網域
    return redirect("{$frontendUrl}/auth/callback?token={$token}");
}
}