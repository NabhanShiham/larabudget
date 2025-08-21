<?php

namespace App\Http\Controllers\Larabudget;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateBudgetRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class UserProfileController extends Controller
{

    /** show the user edit form thingie i forogor */

    public function edit(Request $request): Response
    {
        $user = Auth::user()->load('profile');

        return Inertia::render('Components/LaraProfileEditForm', [
            'profile' => $user->profile ?? [
                'mainbudget' => $user->profile->mainbudget ?? 0,
                'currentspent' => $user->profile->currentspent ?? 0
            ],
        'mustVerifyEmail' => $user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail(),
        'status' => session('status'),
        ]);
    }

    public function update(UpdateBudgetRequest $request): RedirectResponse
    {
        $user = $request->user();
        
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'mainbudget' => $request->mainbudget,
                'currentspent' => $request->spending
            ]
        );

        return redirect()->back()
            ->with('success', 'Budget updated successfully!');
    }


public function show(Request $request)
{
    $user = $request->user();
    $profile = $user->profile;

    $totalSpent = $user->purchases()->sum('amount');

    return response()->json([
        'profile' => $profile ? [
            'mainbudget' => $profile->mainbudget ?? 0,
            'currentspent' => $totalSpent,
        ] : [
            'mainbudget' => 0,
            'currentspent' => $totalSpent,
        ],
        'mustVerifyEmail' => false,
        'status' => session('status')
    ]);
}


    public static function find($id)
    {
        return auth()->user()
            ->profiles()
            ->with('user') 
            ->findOrFail($id);
    }

 
}