<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illumintate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function createProfile(Request $request){
        $user_id = Auth::id();
        $data['user_id'] = $user_id;
        $data = request->validate([
            'id' => 'required|string',
            'mainbudget' => 'required|float|min:0',
            'currentspent' => 'required|float|min:0',
            'user_id' => 'required|integer'
        ]);
    }

    public function getProfile(){
        $user_id = Auth::id();
        
    }
}
