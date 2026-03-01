{{-- resources/views/catalog/show.blade.php --}}
@extends('layouts.app')

@section('title', $book->title)

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Навигация -->
        <div class="mb-6">
            <a href="{{ route('catalog.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
                ← Вернуться в каталог
            </a>
        </div>

        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="md:flex">
                <!-- Обложка книги -->
                <div class="md:w-1/3 p-6 bg-gray-50">
                    @if($book->cover_image)
                        <img src="{{ Storage::url($book->cover_image) }}"
                             alt="{{ $book->title }}"
                             class="w-full rounded-lg shadow-lg">
                    @else
                        <div class="w-full aspect-w-3 aspect-h-4 bg-gradient-to-br from-blue-100 to-blue-200 rounded-lg flex items-center justify-center">
                            <span class="text-blue-600 text-6xl">📚</span>
                        </div>
                    @endif
                </div>

                <!-- Информация о книге -->
                <div class="md:w-2/3 p-6">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $book->title }}</h1>

                    @if($book->subtitle)
                        <p class="text-xl text-gray-600 mb-4">{{ $book->subtitle }}</p>
                    @endif

                    <div class="mb-6">
                        <h2 class="text-sm font-medium text-gray-500 mb-2">Авторы:</h2>
                        <p class="text-lg text-gray-900">
                            @foreach($book->authors as $author)
                                <span class="inline-block bg-blue-50 text-blue-700 px-3 py-1 rounded-full text-sm mr-2 mb-2">
                                {{ $author->full_name }}
                            </span>
                            @endforeach
                        </p>
                    </div>

                    @if($book->description)
                        <div class="mb-6">
                            <h2 class="text-sm font-medium text-gray-500 mb-2">Описание:</h2>
                            <p class="text-gray-700 leading-relaxed">{{ $book->description }}</p>
                        </div>
                    @endif

                    <!-- Характеристики -->
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <p class="text-sm text-gray-500">ISBN</p>
                            <p class="font-medium">{{ $book->isbn }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Издательство</p>
                            <p class="font-medium">{{ $book->publisher->name ?? 'Не указано' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Год издания</p>
                            <p class="font-medium">{{ $book->publication_year ?? 'Не указан' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Страниц</p>
                            <p class="font-medium">{{ $book->pages ?? 'Не указано' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Язык</p>
                            <p class="font-medium">{{ strtoupper($book->language) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Место хранения</p>
                            <p class="font-medium">{{ $book->location ?? 'Не указано' }}{{ $book->shelf ? ', полка ' . $book->shelf : '' }}</p>
                        </div>
                    </div>

                    <!-- Категории -->
                    @if($book->categories->count() > 0)
                        <div class="mb-6">
                            <h2 class="text-sm font-medium text-gray-500 mb-2">Категории:</h2>
                            <div class="flex flex-wrap gap-2">
                                @foreach($book->categories as $category)
                                    <a href="{{ route('catalog.index', ['category' => $category->slug]) }}"
                                       class="text-sm bg-gray-100 text-gray-700 px-3 py-1 rounded-full hover:bg-gray-200">
                                        {{ $category->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Доступность -->
                    <div class="border-t pt-6">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                            <span class="text-2xl font-bold text-gray-900">
                                {{ $book->quantity_available }}
                            </span>
                                <span class="text-gray-600">из {{ $book->quantity_total }} доступно</span>
                            </div>
                            @if($book->isAvailable())
                                <span class="px-4 py-2 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                                ✅ В наличии
                            </span>
                            @else
                                <span class="px-4 py-2 bg-red-100 text-red-800 rounded-full text-sm font-medium">
                                ❌ Нет в наличии
                            </span>
                            @endif
                        </div>

                        @auth
                            @if($book->isAvailable())
                                <form action="{{ route('reader.request-loan') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="book_id" value="{{ $book->id }}">
                                    <button type="submit"
                                            class="w-full bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition font-medium">
                                        Запросить выдачу книги
                                    </button>
                                </form>
                            @else
                                <button disabled
                                        class="w-full bg-gray-300 text-gray-500 px-6 py-3 rounded-lg cursor-not-allowed font-medium">
                                    Нет в наличии
                                </button>
                            @endif
                        @else
                            <a href="{{ route('login') }}"
                               class="block text-center bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition font-medium">
                                Войдите, чтобы взять книгу
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>

        <!-- Похожие книги -->
        @if($relatedBooks->count() > 0)
            <div class="mt-12">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Похожие книги</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($relatedBooks as $relatedBook)
                        <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition">
                            <a href="{{ route('catalog.show', $relatedBook->id) }}" class="block">
                                <div class="aspect-w-3 aspect-h-4 bg-gray-200">
                                    @if($relatedBook->cover_image)
                                        <img src="{{ Storage::url($relatedBook->cover_image) }}"
                                             alt="{{ $relatedBook->title }}"
                                             class="w-full h-40 object-cover">
                                    @else
                                        <div class="w-full h-40 bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center">
                                            <span class="text-blue-600 text-3xl">📚</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="p-3">
                                    <h3 class="font-semibold text-sm text-gray-900 mb-1 line-clamp-2">
                                        {{ $relatedBook->title }}
                                    </h3>
                                    <p class="text-xs text-gray-600">
                                        {{ $relatedBook->main_author?->short_name ?? 'Автор не указан' }}
                                    </p>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endsection
