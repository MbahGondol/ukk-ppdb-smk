<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PPDB SMK Online')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-100">

    <nav class="bg-blue-600 shadow-md">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">

                <a class="text-white text-2xl font-bold" href="{{ route('dashboard') }}">
                    PPDB SMK
                </a>

                @auth
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg">
                            Logout ({{ Auth::user()->name }})
                        </button>
                    </form>
                @endauth

                @guest
                    <div>
                        <a href="{{ route('login') }}" class="text-white hover:text-gray-200 font-medium">Login</a>
                        <a href="{{ route('register') }}" class="ml-4 text-white hover:text-gray-200 font-medium">Register</a>
                    </div>
                @endguest
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-4 mt-6">
        @yield('content')
    </main>

    <footer class="container mx-auto px-4 mt-10 text-center text-gray-500">
        <hr>
        <p class="py-4">&copy; {{ date('Y') }} Proyek UKK. Dibuat dengan Laravel & Tailwind CSS.</p>
    </footer>

</body>
</html>