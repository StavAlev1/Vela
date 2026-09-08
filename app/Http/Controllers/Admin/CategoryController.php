<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    // Route-level middleware (role:admin) already restricts this whole
    // controller, so there's no per-item policy to check here — unlike
    // Posts, Categories don't have an "owner".

    public function index(): View
    {
        $categories = Category::withCount('posts')->orderBy('name')->paginate(15);

        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('admin.categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
        ]);

        $category = Category::create($validated);

        ActivityLog::record(
            'category.created',
            "Created category \"{$category->name}\".",
            $category,
        );

        return redirect()
            ->route('admin.categories.index')
            ->with('success', "Category \"{$category->name}\" created.");
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name,' . $category->id],
        ]);

        $category->update($validated);

        ActivityLog::record(
            'category.updated',
            "Renamed category to \"{$category->name}\".",
            $category,
        );

        return redirect()
            ->route('admin.categories.index')
            ->with('success', "Category \"{$category->name}\" updated.");
    }

    public function destroy(Category $category): RedirectResponse
    {
        $name = $category->name;

        // Posts keep existing (category_id -> null via nullOnDelete), they
        // just become uncategorized.
        $category->delete();

        ActivityLog::record(
            'category.deleted',
            "Deleted category \"{$name}\".",
        );

        return redirect()
            ->route('admin.categories.index')
            ->with('success', "Category \"{$name}\" deleted. Its posts are now uncategorized.");
    }
}
