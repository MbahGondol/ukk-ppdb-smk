<!DOCTYPE html>

<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Halaman Login</title>
<!-- Font Awesome untuk ikon -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap');
body {
    font-family: 'Inter', sans-serif;
    background: linear-gradient(135deg, #4f46e5 0%, #374151 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
    padding: 1rem;
}

.login-card {
    width: 100%;
    max-width: 448px;
    padding: 2.5rem;
    background-color: white;
    border-radius: 1.5rem;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    transition: all 0.5s ease;
    text-align: center;
}

.login-card:hover {
    box-shadow: 0 25px 80px rgba(0, 0, 0, 0.4);
}

.icon-container {
    display: inline-block;
    padding: 1rem;
    background-color: #6366f1;
    border-radius: 1rem;
    margin-bottom: 1rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.login-card h2 {
    font-size: 1.875rem;
    font-weight: 800;
    color: #1f2937;
}

.login-card .subtitle {
    margin-top: 0.5rem;
    font-size: 0.875rem;
    color: #6b7280;
    margin-bottom: 1rem;
}

.error-alert {
    padding: 1rem;
    font-size: 0.875rem;
    color: #b91c1c;
    background-color: #fee2e2;
    border: 1px solid #f87171;
    border-radius: 0.75rem;
    font-weight: 500;
    text-align: left;
    margin-bottom: 1.5rem;
    margin-top: 1.5rem;
}

.form-group {
    text-align: left;
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    font-size: 0.875rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.25rem;
}

.form-group input {
    width: 100%;
    padding: 12px 16px;
    border: 1px solid #d1d5db;
    border-radius: 0.75rem;
    box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.05);
    transition: all 0.2s ease;
    font-size: 1rem;
}

.form-group input:focus {
    outline: none;
    border-color: #4f46e5;
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.25);
}

.form-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.remember-me label {
    font-weight: 400;
    color: #1f2937;
}

.login-button {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 12px 16px;
    border: none;
    border-radius: 0.75rem;
    background-color: #4f46e5;
    color: white;
    font-size: 1rem;
    font-weight: 700;
    cursor: pointer;
    box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.3), 0 4px 6px -4px rgba(79, 70, 229, 0.1);
    transition: background-color 0.2s ease, transform 0.2s ease;
}

.login-button:hover {
    background-color: #4338ca;
    transform: scale(1.01);
}

.register-link-box {
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid #e5e7eb;
}

.register-link-box a {
    display: inline-flex;
    align-items: center;
    color: #4f46e5;
    font-weight: 600;
    transition: color 0.2s ease;
}

.register-link-box a:hover {
    color: #4338ca;
}

</style>
</head>
<body>

<div class="login-card">

<div>
    <div class="icon-container">
        <i class="fas fa-user-lock" style="font-size: 2rem; color: white;"></i>
    </div>
    <h2>Masuk ke Sistem PPDB</h2>
    <p class="subtitle">Silakan masuk menggunakan akun Siswa atau Admin Anda.</p>
</div>

<!-- Notifikasi Error dari Session -->
@if (session('error'))
    <div class="error-alert">
        <i class="fas fa-exclamation-triangle" style="margin-right: 0.5rem;"></i> {{ session('error') }}
    </div>
@endif

<!-- Error validation message (jika ada error dari $errors) -->
@if ($errors->any())
    <div class="error-alert">
        <i class="fas fa-exclamation-circle" style="margin-right: 0.5rem;"></i>
        {{ $errors->first('email') }}
    </div>
@endif

<!-- Formulir Login -->
<form style="margin-top: 1.5rem;" action="{{ route('login.post') }}" method="POST">
    @csrf

    <div class="form-group">
        <label for="email">Alamat Email</label>
        <input id="email" name="email" type="email" autocomplete="email" required
               placeholder="Masukkan Email Anda" value="{{ old('email') }}">
    </div>

    <div class="form-group">
        <label for="password">Password</label>
        <input id="password" name="password" type="password" autocomplete="current-password" required
               placeholder="Masukkan Password Anda">
    </div>

    <div class="form-actions">
        <div class="remember-me" style="display: flex; align-items: center;">
            <input id="remember" name="remember" type="checkbox"
                   style="height: 1rem; width: 1rem; color: #4f46e5; border-radius: 0.25rem; margin-right: 0.5rem; cursor: pointer;">
            <label for="remember">Ingat Saya</label>
        </div>
    </div>

    <div>
        <button type="submit" class="login-button">
            <i class="fas fa-sign-in-alt" style="margin-right: 0.5rem;"></i> MASUK KE AKUN
        </button>
    </div>
</form>

<div class="register-link-box">
    <p style="font-size: 0.875rem; color: #4b5563; margin-bottom: 0.75rem;">
        Belum punya akun pendaftaran?
    </p>
    <a href="{{ route('register') }}">
        <i class="fas fa-user-plus" style="margin-right: 0.5rem;"></i> DAFTAR SEBAGAI CALON SISWA
    </a>
</div>

</div>

</body>
</html>