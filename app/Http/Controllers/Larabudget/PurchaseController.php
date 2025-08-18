<?php

namespace App\Http\Controllers\Larabudget;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
public function store(Request $request)
{
    $validated = $request->validate([
        'amount' => 'required|numeric|min:0.01',
        'description' => 'nullable|string|max:255',
        'transaction_date' => 'required|date',
        'category_id' => 'required|exists:categories,id',
        'receipt' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048'
    ]);

    if ($request->hasFile('receipt')) {
        $validated['receipt_path'] = $request->file('receipt')->store('receipts');
    }

    $purchase = auth()->user()->purchases()->create([
        'category_id' => $validated['category_id'],
        'amount' => $validated['amount'],
        'description' => $validated['description'],
        'transaction_date' => $validated['transaction_date'],
        'receipt_path' => $validated['receipt_path'] ?? null
    ]);

    $category = CategoryController::find($validated['category_id']);
    $category->current_spent += $validated['amount'];
    $category->save();

    $profile = auth()->user()->profile;
    $profile->currentspent += $validated['amount'];
    $profile->save();

    
return response()->json([
    'success' => true,
    'purchase' => $purchase,
    'category' => $category->fresh()
], 200);

}
}
