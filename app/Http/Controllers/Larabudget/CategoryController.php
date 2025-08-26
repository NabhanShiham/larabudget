<?php

namespace App\Http\Controllers\Larabudget;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class CategoryController extends Controller
{
     use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Categories/Index', [
            'categories' => auth()->user()->categories()
                ->withSum('receipts', 'amount')
                ->get()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Categories/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'budgeted_amount' => 'required|numeric|min:0'
        ]);

        auth()->user()->categories()->create($validated);

        return redirect()->back();
    }

    /**
     * Fetch categories that belong to the user
     */
    public function show(Request $request)
    {
        $user = $request->user();
        $categories = $user->categories()->get()->map(function($category){
            return [
                'id' => $category->id,
                'name' => $category->name,
                'budgeted_amount' => $category->budgeted_amount,
                'current_spent' => $category->current_spent
            ];
        });
        return response()->json([
            'categories' => $categories,
            'status' => session('status')
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $this->authorize('update', $category);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'budgeted_amount' => 'sometimes|numeric|min:0'
        ]);

        $category->update($validated);

        return redirect()->back()->with('success', 'Category updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        if(auth()->id() !== $category->user_id){
            abort(403, 'Unauthorized action');
        }
        $category->delete();
        
        return redirect()->back()->with([
                'success' => 'Category deleted successfully'
            ]);
    }

    public static function find($id)
    {
        return auth()->user()
            ->categories()
            ->with('user') 
            ->findOrFail($id);
    }
}
