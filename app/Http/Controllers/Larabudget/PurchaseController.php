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
        $path = $request->file('receipt')->store('receipts');
        $validated['receipt_path'] = $path;
    }

    $purchase = auth()->user()->purchases()->create($validated);
    
    $category = Category::find($validated['category_id']);
    $category->increment('current_spent', $validated['amount']);

    return response()->json([
        'purchase' => $purchase,
        'category' => $category->fresh()
    ]);
}
}
