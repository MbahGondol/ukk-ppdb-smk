<!-- resources/views/admin/layouts/app.blade.php -->

<!DOCTYPE html>

<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin PPDB | @yield('title', 'Dashboard')</title>
<!-- Font Awesome untuk ikon (Dipertahankan karena penting) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap');
    :root {
        --color-primary: #4338ca; /* Indigo-700 */
        --color-secondary: #eef2ff; /* Indigo-50 */
        --color-dark: #1f2937; /* Gray-800 */
        --color-bg: #f9fafb; /* Gray-50 */
    }

    body { 
        font-family: 'Inter', sans-serif;
        margin: 0;
        background-color: var(--color-bg);
    }

    .layout-container {
        display: flex;
        min-height: 100vh;
    }

    /* SIDEBAR STYLES */
    .sidebar {
        width: 256px; /* w-64 */
        background-color: var(--color-dark);
        box-shadow: 4px 0 10px rgba(0, 0, 0, 0.2);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: fixed;
        height: 100%;
    }

    .sidebar h1 {
        font-size: 1.875rem; /* text-3xl */
        font-weight: 800; /* font-extrabold */
        color: #818cf8; /* Indigo-400 */
        margin-bottom: 2.5rem; /* mb-10 */
        letter-spacing: 0.05em; /* tracking-wider */
    }

    .sidebar nav a {
        display: flex;
        align-items: center;
        padding: 12px; /* p-3 */
        color: #d1d5db; /* Gray-300 */
        border-radius: 0.75rem; /* rounded-xl */
        transition: all 0.2s ease-in-out;
        margin-top: 0.5rem;
    }
    
    .sidebar nav a:hover {
        background-color: rgba(75, 85, 99, 0.7); /* gray-700/70 */
        color: white;
    }
    
    .sidebar nav a.active {
        background-color: var(--color-primary);
        color: white;
        font-weight: 600;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .sidebar nav .nav-category {
        font-size: 0.75rem; /* text-xs */
        font-weight: 600; /* font-semibold */
        color: #9ca3af; /* gray-500 */
        text-transform: uppercase;
        margin-top: 2rem;
        margin-bottom: 0.5rem;
        letter-spacing: 0.1em; /* tracking-widest */
        padding-left: 12px;
    }

    .sidebar i {
        width: 20px; /* w-5 */
        height: 20px; /* h-5 */
        margin-right: 0.75rem; /* mr-3 */
    }

    .sidebar .logout-section {
        padding: 1.5rem;
        border-top: 1px solid #374151; /* gray-700 */
    }

    .sidebar .logout-section button {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 12px;
        color: #f87171; /* red-400 */
        border: none;
        border-radius: 0.75rem;
        background-color: rgba(75, 85, 99, 0.5);
        transition: all 0.2s ease-in-out;
        cursor: pointer;
        font-weight: 500;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .sidebar .logout-section button:hover {
        background-color: #dc2626; /* red-600 */
        color: white;
    }


    /* MAIN CONTENT STYLES */
    .main-wrapper {
        flex: 1;
        margin-left: 256px; /* Offset for fixed sidebar */
        display: flex;
        flex-direction: column;
        overflow-x: hidden;
    }

    .header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem 2rem; /* p-4 / p-8 */
        background-color: white;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.06); /* shadow-lg */
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .header h2 {
        font-size: 1.5rem; /* text-2xl */
        font-weight: 700; /* font-bold */
        color: #1f2937; /* gray-800 */
    }

    .header .user-info {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    
    .header .user-info .text-sm {
        font-size: 0.875rem;
        color: #4b5563; /* gray-600 */
        font-weight: 500;
    }
    
    .header .user-info .user-name {
        color: #4f46e5; /* indigo-600 */
    }

    .header .avatar {
        height: 40px;
        width: 40px;
        background-color: #6366f1; /* indigo-500 */
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 0.875rem; /* text-sm */
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .page-content {
        flex: 1;
        padding: 2rem; /* p-8 */
        overflow-y: auto;
    }

    /* Responsiveness for smaller screens */
    @media (max-width: 1024px) {
        .sidebar {
            width: 0;
            display: none; /* Hide sidebar completely on smaller screens for simplicity */
        }
        .main-wrapper {
            margin-left: 0;
        }
        .header {
            padding: 1rem;
        }
    }
</style>

</head>
<body>

<div class="layout-container">
<!-- Sidebar -->
<aside class="sidebar">
<div style="padding: 1.5rem;">
<h1>PPDB Admin</h1>

        <nav>
            <!-- Dashboard -->
            <a href="{{ route('admin.dashboard') }}" 
               class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
            
            <h2 class="nav-category">Modul</h2>
            
            <!-- Calon Siswa -->
            <a href="#" class="nav-link">
                <i class="fas fa-users"></i>
                <span>Calon Siswa</span>
            </a>
            <!-- Biaya & Kuota -->
            <a href="#" class="nav-link">
                <i class="fas fa-money-check-alt"></i>
                <span>Biaya & Kuota</span>
            </a>
            <!-- Konfigurasi -->
            <a href="#" class="nav-link">
                <i class="fas fa-cogs"></i>
                <span>Konfigurasi Sistem</span>
            </a>
        </nav>
    </div>
    
    <!-- Logout Section -->
    <div class="logout-section">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </button>
        </form>
    </div>
</aside>

<!-- Main Content Wrapper -->
<div class="main-wrapper">
    <!-- Header/Topbar -->
    <header class="header">
        <h2>@yield('title', 'Dashboard')</h2>
        <div class="user-info">
            <span class="text-sm">Halo, <span class="user-name">{{ Auth::user()->name ?? 'Admin' }}</span>!</span>
            <!-- Avatar placeholder -->
            <div class="avatar">
                <i class="fas fa-shield-alt" style="font-size: 0.85rem;"></i>
            </div>
        </div>
    </header>

    <!-- Page Content -->
    <div class="page-content">
        @yield('content')
    </div>
</div>

</div>

</body>
</html>