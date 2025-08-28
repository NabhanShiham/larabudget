<?php

namespace App\Http\Controllers\Socialite;

use App\Http\Controllers\Controller;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;


class ProviderCallbackController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(string $provider)
    {
        if(!in_array($provider, ['github'])){
            return redirect(route('login'))->withErrors(['provider' => 'Invalid provider']);
        }
    $socialUser = Socialite::driver($provider)->user();
 
    $user = User::updateOrCreate([
        'provider_id' => $socialUser->id,
        'provider_name' => $provider,
    ], [
        'name' => $socialUser->name,
        'email' => $socialUser->email,
        'provider_token' => $socialUser->token,
        'provider_refresh_token' => $socialUser->refreshToken,
    ]);
 
    Auth::login($user);
 
    return redirect('dashboard');
    }
}
