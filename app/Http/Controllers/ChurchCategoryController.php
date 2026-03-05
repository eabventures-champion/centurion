<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreChurchCategoryRequest;
use App\Models\ChurchCategory;
use App\Services\ChurchService;
use Illuminate\Http\Request;

class ChurchCategoryController extends Controller
{
    protected $churchService;

    public function __construct(ChurchService $churchService)
    {
        $this->churchService = $churchService;
    }

    public function index()
    {
        $categories = ChurchCategory::all();
        return view('church-categories.index', compact('categories'));
    }

    public function store(StoreChurchCategoryRequest $request)
    {
        $this->churchService->createCategory($request->validated());
        return redirect()->back()->with('success', 'Category created successfully!');
    }

    public function show(ChurchCategory $churchCategory)
    {
        return view('church-categories.show', compact('churchCategory'));
    }

    public function edit(ChurchCategory $churchCategory)
    {
        return view('church-categories.edit', compact('churchCategory'));
    }

    public function update(Request $request, ChurchCategory $churchCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:church_categories,name,' . $churchCategory->id,
            'zonal_pastor_name' => 'nullable|string|max:255',
        ]);

        $churchCategory->update($validated);

        return redirect()->route('church-categories.index')->with('success', 'Category updated successfully!');
    }

    public function destroy(ChurchCategory $churchCategory)
    {
        $churchCategory->delete();
        return redirect()->back()->with('success', 'Category deleted successfully!');
    }
}
