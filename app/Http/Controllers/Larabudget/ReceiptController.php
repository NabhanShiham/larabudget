<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReceiptController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'image' => 'required|image|max:2048',
            'amount' => 'required|numeric|min:0.01',
            'category_id' => 'required|exists:categories,id'
        ]);

        $path = $request->file('image')->store('receipts');

        // Create record
        $receipt = Receipt::create([
            'user_id' => auth()->id(),
            'category_id' => $validated['category_id'],
            'amount' => $validated['amount'],
            'image_path' => $path
        ]);

        $receipt->category->increment('current_spent', $validated['amount']);

        return back()->with('success', 'Receipt uploaded!');
    }

    public function destroy(Receipt $receipt)
    {
        $this->authorize('delete', $receipt);
        
        $receipt->category->decrement('current_spent', $receipt->amount);
        
        Storage::delete($receipt->image_path);
        $receipt->delete();

        return back()->with('warning', 'Receipt deleted');
    }
}