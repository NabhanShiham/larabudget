<?php

namespace App\Http\Controllers\Socialite;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;


class ProviderRedirectController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(string $provider)
    {
        if(!in_array($provider, ['github'])){
            return redirect(route('login'))->withErrors(['provider' => 'Invalid Provider']);
        }
        try {
        return Inertia::location(Socialite::driver($provider)->redirect());
        }catch(error){
            return redirect(route('login'))->withErrors(['provider' => 'Something went wrong']); 
        }
    }
}
