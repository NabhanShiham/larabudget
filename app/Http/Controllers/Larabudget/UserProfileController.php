<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class UserProfileController extends Controller
{
    // public function createProfile(Request $request){
    //     $user_id = Auth::id();
    //     $data['user_id'] = $user_id;
    //     $data = request->validate([
    //         'id' => 'required|string',
    //         'mainbudget' => 'required|float|min:0',
    //         'currentspent' => 'required|float|min:0',
    //         'user_id' => 'required|integer'
    //     ]);
    //     $user = auth()->user();
    // }

    // public function getProfile(){
    //     $user_id = Auth::id();
        
    // }

    /**
    * Show the user's larabudget profile edit form.
    */
    public function edit(Request $request):Response
    {
            return Inertia::render('/resources/js/components/LaraProfileEditForm', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => $request->session()->get('status'),
            ]);
    }

    public function update(){
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return to_route('profile.edit');
    }
}
