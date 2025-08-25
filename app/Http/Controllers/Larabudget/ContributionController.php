<?php

namespace App\Http\Controllers\Larabudget;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ContributionController extends Controller
{
    public function store(Request $request){
        $path = $request->file('receipt')->store('receipts');
        Contribution::create([
            'collaborate_id' => $request->collaborate_id,
            'user_id' => auth()->id(),
            'amount' => $request->amount,
            'receipt_path' => $path,
        ]);
        return redirect()->back()->with('success', 'Contribution Added!');
    }
}
