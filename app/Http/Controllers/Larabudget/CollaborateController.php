<?php

namespace App\Http\Controllers\Larabudget;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Collaborate;

class CollaborateController extends Controller
{
    public function index(){
        $collaborations = auth()->user()->collaborations()->with('owner')->get();
        return view('collaborations.index', compact('collaborations'));
    }
    
    public function store(Request $request){
        $collab = Collaborate::create([
            'ownerId' => auth()->id(),
            'goal' => $request->goal,
            'currentProgress' => 0,
            'status' => 'pending',
        ]);
        $collab->members()->attach($request->members);
        return redirect()->back();
    }
}
