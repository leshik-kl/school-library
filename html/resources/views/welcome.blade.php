{{-- resources/views/layouts/app.blade.php --}}
    <!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Школьная библиотека')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-50">
<nav class="bg-white shadow-lg border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="flex-shrink-0 flex items-center">
                    <a href="{{ url('/') }}" class="text-xl font-bold text-blue-600">
                        📚 Школьная библиотека
                    </a>
                </div>
                <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                    <a href="{{ route('catalog.index') }}"
                       class="inline-flex items-center px-1 pt-1 text-sm font-medium {{ request()->routeIs('catalog.*') ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-700 hover:text-gray-900' }}">
                        Каталог
                    </a>
                    @auth
                        <a href="{{ route('reader.profile') }}"
                           class="inline-flex items-center px-1 pt-1 text-sm font-medium {{ request()->routeIs('reader.*') ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-700 hover:text-gray-900' }}">
                            Личный кабинет
                        </a>
                    @endauth
                </div>
            </div>
            <div class="hidden sm:ml-6 sm:flex sm:items-center">
                @auth
                    <span class="text-sm text-gray-700 mr-4">{{ Auth::user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-gray-700 hover:text-gray-900">
                            Выйти
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-sm text-gray-700 hover:text-gray-900 mr-4">
                        Войти
                    </a>
                    <a href="{{ route('register') }}" class="text-sm bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                        Регистрация
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>

<main class="py-8">
    @yield('content')
</main>

<footer class="bg-white border-t border-gray-200 mt-auto">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <p class="text-center text-sm text-gray-500">
            © {{ date('Y') }} Школьная библиотека. Все права защищены.
        </p>
    </div>
</footer>

@stack('scripts')
</body>
</html>
