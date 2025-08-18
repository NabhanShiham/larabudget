<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ReceiptController extends Controller
{
    public function store(Request $request, Category $category)
{
    $validated = $request->validate([
        'amount' => 'required|numeric|min:0.01',
        'description' => 'nullable|string',
        'receipt_image' => 'nullable|image|max:2048'
    ]);

    if ($request->hasFile('receipt_image')) {
        $path = $request->file('receipt_image')->store('receipts');
        $validated['image_path'] = $path;
    }

    $category->receipts()->create(array_merge(
        $validated,
        ['user_id' => auth()->id()]
    ));

    $category->current_spent += $validated['amount'];
    $category->save();

    return back()->with('success', 'Receipt added!');
}
public function destroy(Receipt $receipt)
{
    $this->authorize('delete', $receipt);

    $receipt->category->current_spent -= $receipt->amount;
    $receipt->category->save();

    Storage::delete($receipt->image_path);
    $receipt->delete();

    return back()->with('warning', 'Receipt deleted');
}
}
