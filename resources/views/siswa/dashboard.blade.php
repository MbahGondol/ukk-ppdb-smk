<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard Siswa</title>
<!-- Font Awesome untuk ikon -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap');
    body {
        font-family: 'Inter', sans-serif;
        background-color: #f3f4f6;
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
    }

    .siswa-card {
        background-color: white;
        padding: 3rem;
        border-radius: 1rem;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        text-align: center;
        max-width: 600px;
        width: 90%;
        border-top: 5px solid #10b981;
    }

    .siswa-card h1 {
        font-size: 1.875rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 0.5rem;
    }

    .siswa-card p {
        color: #4b5563;
        margin-bottom: 1.5rem;
    }
    
    .logout-btn {
        background-color: #dc2626;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 0.5rem;
        cursor: pointer;
        transition: background-color 0.2s ease;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
    }
    
    .logout-btn:hover {
        background-color: #b91c1c;
    }
</style>

</head>
<body>
<div class="siswa-card">
<i class="fas fa-check-circle" style="font-size: 3rem; color: #10b981; margin-bottom: 1rem;"></i>
<h1>Selamat Datang, {{ $user->name }}!</h1>
<p>Anda berhasil masuk sebagai Calon Siswa. Di sini Anda akan mengisi formulir pendaftaran, mengunggah dokumen, dan memantau status aplikasi Anda.</p>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="logout-btn">
            <i class="fas fa-sign-out-alt" style="margin-right: 0.5rem;"></i>
            Logout
        </button>
    </form>
</div>

</body>
</html>