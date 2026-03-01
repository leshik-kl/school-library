@extends('layouts.app')

@section('title', 'Каталог книг')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Заголовок -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Каталог книг</h1>
        <p class="mt-2 text-sm text-gray-600">
            Всего книг в библиотеке: {{ $books->total() }}
        </p>
    </div>

    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Боковая панель с фильтрами -->
        <div class="lg:w-64 flex-shrink-0">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Фильтры</h2>
                
                <!-- Поиск -->
                <form action="{{ route('catalog.index') }}" method="GET" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Поиск
                        </label>
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Название или автор..."
                               class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <!-- Категории -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Категория
                        </label>
                        <select name="category" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Все категории</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->slug }}" {{ request('category') == $category->slug ? 'selected' : '' }}>
                                    {{ $category->name }} ({{ $category->books_count ?? $category->books->count() }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Год издания -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Год издания
                        </label>
                        <input type="number" 
                               name="year" 
                               value="{{ request('year') }}"
                               placeholder="Год"
                               class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <!-- Сортировка -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Сортировка
                        </label>
                        <select name="sort" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            <option value="title" {{ request('sort', 'title') == 'title' ? 'selected' : '' }}>По названию</option>
                            <option value="publication_year" {{ request('sort') == 'publication_year' ? 'selected' : '' }}>По году</option>
                            <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>По дате добавления</option>
                        </select>
                    </div>

                    <div>
                        <select name="order" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            <option value="asc" {{ request('order', 'asc') == 'asc' ? 'selected' : '' }}>По возрастанию</option>
                            <option value="desc" {{ request('order') == 'desc' ? 'selected' : '' }}>По убыванию</option>
                        </select>
                    </div>

                    <button type="submit" 
                            class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                        Применить
                    </button>

                    @if(request()->anyFilled(['search', 'category', 'year', 'sort', 'order']))
                        <a href="{{ route('catalog.index') }}" 
                           class="block text-center text-sm text-gray-600 hover:text-gray-900">
                            Сбросить фильтры
                        </a>
                    @endif
                </form>
            </div>
        </div>

        <!-- Сетка книг -->
        <div class="flex-1">
            @if($books->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($books as $book)
                        <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition">
                            <a href="{{ route('catalog.show', $book->id) }}" class="block">
                                <div class="aspect-w-3 aspect-h-4 bg-gray-200">
                                    @if($book->cover_image)
                                        <img src="{{ Storage::url($book->cover_image) }}" 
                                             alt="{{ $book->title }}"
                                             class="w-full h-48 object-cover">
                                    @else
                                        <div class="w-full h-48 bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center">
                                            <span class="text-blue-600 text-4xl">📚</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="p-4">
                                    <h3 class="font-semibold text-lg text-gray-900 mb-1 line-clamp-2">
                                        {{ $book->title }}
                                    </h3>
                                    <p class="text-sm text-gray-600 mb-2">
                                        {{ $book->authors->pluck('full_name')->implode(', ') }}
                                    </p>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-500">
                                            {{ $book->publication_year ?? 'Год не указан' }}
                                        </span>
                                        @if($book->isAvailable())
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                В наличии
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                Нет в наличии
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>

                <!-- Пагинация -->
                <div class="mt-8">
                    {{ $books->withQueryString()->links() }}
                </div>
            @else
                <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                    <div class="text-6xl mb-4">📖</div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">
                        Книги не найдены
                    </h3>
                    <p class="text-gray-600">
                        Попробуйте изменить параметры поиска
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
