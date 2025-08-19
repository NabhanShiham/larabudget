<?php

namespace App\Http\Controllers\Larabudget;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function index(Request $request)
{
    $request->validate([
        'category_ids' => 'required|string'
    ]);

    $categoryIds = explode(',', $request->category_ids);

    return Purchase::whereIn('category_id', $categoryIds)
        ->latest()
        ->get()
        ->map(function ($purchase) {
            return [
                'id' => $purchase->id,
                'category_id' => $purchase->category_id,
                'amount' => $purchase->amount,
                'description' => $purchase->description,
                'transaction_date' => $purchase->transaction_date,
                'created_at' => $purchase->created_at,
                'receipt_path' => $purchase->receipt_path 
            ];
        });
}
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

public function showCategoryPurchases(Request $request)
{
    $user = $request->user();
    $limit = 5; 
    
    $categories = $user->categories()
        ->with(['purchases' => function($query) use ($limit) {
            $query->latest()
                ->take($limit)
                ->select(['id', 'category_id', 'amount', 'description', 'transaction_date', 'created_at', 'receipt_path']);
        }])
        ->get()
        ->map(function($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'budgeted_amount' => $category->budgeted_amount,
                'current_spent' => $category->current_spent,
                'purchases' => $category->purchases->map(function($purchase) {
                    return [
                        'id' => $purchase->id,
                        'amount' => $purchase->amount,
                        'description' => $purchase->description,
                        'transaction_date' => $purchase->transaction_date,
                        'created_at' => $purchase->created_at,
                        'receipt_path' => $purchase->receipt_path,
                    ];
                })
            ];
        });
    
    return response()->json([
        'categories' => $categories,
        'status' => session('status')
    ]);
}

}
