<?php
// app/Http/Controllers/CatalogController.php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::with(['authors', 'categories'])
            ->where('status', 'available')
            ->where('quantity_available', '>', 0);

        // Поиск по названию или автору
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'ilike', "%{$search}%")
                    ->orWhere('isbn', 'ilike', "%{$search}%")
                    ->orWhereHas('authors', function($q) use ($search) {
                        $q->where('last_name', 'ilike', "%{$search}%")
                            ->orWhere('first_name', 'ilike', "%{$search}%");
                    });
            });
        }

        // Фильтр по категории
        if ($request->filled('category')) {
            $query->whereHas('categories', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Фильтр по году
        if ($request->filled('year')) {
            $query->where('publication_year', $request->year);
        }

        // Сортировка
        $sort = $request->get('sort', 'title');
        $order = $request->get('order', 'asc');
        $query->orderBy($sort, $order);

        $books = $query->paginate(12);

        // Получаем все категории и фильтруем в PHP
        $allCategories = Category::all();
        $categories = $allCategories->filter(function($category) {
            return $category->books()->count() > 0;
        });

        return view('catalog.index', compact('books', 'categories'));
    }

    public function show($id)
    {
        $book = Book::with(['authors', 'categories', 'publisher'])
            ->findOrFail($id);

        $relatedBooks = Book::with(['authors'])
            ->whereHas('categories', function($q) use ($book) {
                $q->whereIn('categories.id', $book->categories->pluck('id'));
            })
            ->where('id', '!=', $book->id)
            ->where('status', 'available')
            ->limit(4)
            ->get();

        return view('catalog.show', compact('book', 'relatedBooks'));
    }
}
