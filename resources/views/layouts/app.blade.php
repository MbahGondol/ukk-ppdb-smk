<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PPDB SMK Pejantan Tangguh')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-100 flex flex-col min-h-screen">

    <nav class="bg-blue-600 shadow-md">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                
                <a class="flex items-center text-white text-2xl font-bold" href="{{ route('beranda') }}">
                    <svg class="w-8 h-8 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z" />
                    </svg>
                    SMK Pejantan Tangguh
                </a>
                
                <div class="flex items-center flex-wrap">
                    <a href="{{ route('beranda') }}" class="text-white hover:text-gray-200 font-medium px-3">Beranda</a>
                    <a href="{{ route('profil') }}" class="text-white hover:text-gray-200 font-medium px-3">Profil Sekolah</a>
                    <a href="{{ route('info.jurusan') }}" class="text-white hover:text-gray-200 font-medium px-3">Info Jurusan</a>
                    <div class="border-l border-blue-400 h-6 mx-3"></div>
                    
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-white hover:text-gray-200 font-medium px-3">
                            My Dashboard
                        </a>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg">
                                Logout
                            </button>
                        </form>
                    @endauth

                    @guest
                        <a href="{{ route('login') }}" class="text-white hover:text-gray-200 font-medium">Login</a>
                        <a href="{{ route('register') }}" class="ml-4 bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg">
                            Register
                        </a>
                    @endguest
                </div>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-4 mt-6 flex-grow">
        @yield('content')
    </main>

    <footer class="container mx-auto px-4 mt-10 text-center text-gray-500">
        <hr>
        <p class="py-4">&copy; {{ date('Y') }} SMK Pejantan Tangguh. Dibuat dengan Laravel 12 & Tailwind CSS.</p>
    </footer>

</body>
</html>