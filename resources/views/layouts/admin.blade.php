<!DOCTYPE html>
<html lang="id" class="h-screen overflow-hidden bg-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') - PPDB SMK</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-screen font-sans antialiased overflow-hidden">

    <div class="flex h-screen bg-gray-100">
        
        <aside class="w-64 bg-blue-800 text-white flex flex-col flex-shrink-0 transition-all duration-300 h-full">
            
            <div class="h-16 flex items-center justify-center border-b border-blue-700 bg-blue-900 flex-shrink-0">
                <h1 class="text-xl font-bold tracking-wider">ADMIN PANEL</h1>
            </div>

            <nav class="flex-1 px-2 py-4 space-y-2 overflow-y-auto">
                
                <a href="{{ route('beranda') }}" class="flex items-center px-4 py-2 mb-4 bg-blue-900 rounded-lg text-blue-200 hover:text-white hover:bg-blue-700 transition-colors border border-blue-700">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                    Lihat Website Utama
                </a>

                <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors {{ request()->routeIs('dashboard') ? 'bg-blue-700' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    Dashboard
                </a>

                <div class="px-4 pt-4 pb-2 text-xs font-bold text-blue-300 uppercase tracking-wider">
                    Master Data
                </div>

                <a href="{{ route('admin.jurusan.index') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors {{ request()->routeIs('admin.jurusan.*') ? 'bg-blue-700' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    Jurusan
                </a>

                <a href="{{ route('admin.gelombang.index') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors {{ request()->routeIs('admin.gelombang.*') ? 'bg-blue-700' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Gelombang Pendaftaran
                </a>

                <a href="{{ route('admin.promo.index') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors {{ request()->routeIs('admin.promo.*') ? 'bg-blue-700' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                    Kode Promo
                </a>

                <a href="{{ route('admin.kuota.index') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors {{ request()->routeIs('admin.kuota.*') ? 'bg-blue-700' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    Manajemen Kuota
                </a>

                <div class="px-4 pt-4 pb-2 text-xs font-bold text-blue-300 uppercase tracking-wider">
                    Keuangan
                </div>

                <a href="{{ route('admin.jenis-biaya.index') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors {{ request()->routeIs('admin.jenis-biaya.*') ? 'bg-blue-700' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                    Tipe Biaya
                </a>

                <a href="{{ route('admin.biaya.index') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors {{ request()->routeIs('admin.biaya.*') ? 'bg-blue-700' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Daftar Harga
                </a>

                <div class="px-4 pt-4 pb-2 text-xs font-bold text-blue-300 uppercase tracking-wider">
                    Pendaftaran
                </div>

                <a href="{{ route('admin.pendaftar.index') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors {{ request()->routeIs('admin.pendaftar.*') ? 'bg-blue-700' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Manajemen Pendaftar
                </a>

            </nav>

            <div class="p-4 border-t border-blue-700 flex-shrink-0">
                <div class="flex items-center mb-4">
                    <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-sm font-bold">
                        A
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-blue-300">Administrator</p>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white text-sm font-bold py-2 px-4 rounded transition">
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        <div class="flex-1 flex flex-col overflow-hidden">
            
            <header class="bg-white shadow-sm h-16 flex items-center px-6 flex-shrink-0">
                <h2 class="text-xl font-bold text-gray-800">@yield('header', 'Dashboard')</h2>
            </header>

            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
                @yield('content')
            </main>
        </div>

    </div>

</body>
</html>