<!DOCTYPE html>

<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Daftar Akun Baru PPDB</title>
<!-- Font Awesome untuk ikon -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap');

body {
    font-family: 'Inter', sans-serif;
    background: linear-gradient(135deg, #059669 0%, #374151 100%); /* Greenish gradient for registration */
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
    padding: 1rem;
}

.register-card {
    width: 100%;
    max-width: 448px;
    padding: 2.5rem;
    background-color: white;
    border-radius: 1.5rem;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    transition: all 0.5s ease;
    text-align: center;
}

.register-card:hover {
    box-shadow: 0 25px 80px rgba(0, 0, 0, 0.4);
}

.icon-container {
    display: inline-block;
    padding: 1rem;
    background-color: #10b981; /* Green-500 */
    border-radius: 1rem;
    margin-bottom: 1rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.register-card h2 {
    font-size: 1.875rem;
    font-weight: 800;
    color: #1f2937;
}

.register-card .subtitle {
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
    margin-bottom: 1rem;
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
    border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.25);
}

.register-button {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 12px 16px;
    border: none;
    border-radius: 0.75rem;
    background-color: #10b981; /* Green-600 */
    color: white;
    font-size: 1rem;
    font-weight: 700;
    cursor: pointer;
    box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.3), 0 4px 6px -4px rgba(16, 185, 129, 0.1);
    transition: background-color 0.2s ease, transform 0.2s ease;
    margin-top: 1.5rem;
}

.register-button:hover {
    background-color: #059669; /* Green-700 */
    transform: scale(1.01);
}

.login-link-box {
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid #e5e7eb;
}

.login-link-box a {
    display: inline-flex;
    align-items: center;
    color: #4f46e5;
    font-weight: 600;
    transition: color 0.2s ease;
}

.login-link-box a:hover {
    color: #4338ca;
}

</style>
</head>
<body>

<div class="register-card">

<div>
    <div class="icon-container">
        <i class="fas fa-id-card" style="font-size: 2rem; color: white;"></i>
    </div>
    <h2>Daftar Akun Pendaftaran</h2>
    <p class="subtitle">Isi data di bawah untuk membuat akun calon siswa baru.</p>
</div>

<!-- Tampilkan semua error validasi di sini -->
@if ($errors->any())
    <div class="error-alert">
        <i class="fas fa-exclamation-circle" style="margin-right: 0.5rem;"></i>
        Pendaftaran gagal. Mohon periksa kembali input Anda.
        <ul style="margin-top: 0.5rem; list-style: disc; margin-left: 1.5rem;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form style="margin-top: 1.5rem;" action="{{ route('register.post') }}" method="POST">
    @csrf

    <div class="form-group">
        <label for="name">Nama Lengkap</label>
        <input id="name" name="name" type="text" required
               placeholder="Nama sesuai Akta Kelahiran" value="{{ old('name') }}">
    </div>
    
    <div class="form-group">
        <label for="email">Alamat Email</label>
        <input id="email" name="email" type="email" autocomplete="email" required
               placeholder="contoh@gmail.com" value="{{ old('email') }}">
    </div>

    <div class="form-group">
        <label for="password">Password</label>
        <input id="password" name="password" type="password" required
               placeholder="Minimal 8 karakter">
    </div>

    <div class="form-group">
        <label for="password_confirmation">Konfirmasi Password</label>
        <input id="password_confirmation" name="password_confirmation" type="password" required
               placeholder="Ulangi password di atas">
    </div>


    <div>
        <button type="submit" class="register-button">
            <i class="fas fa-paper-plane" style="margin-right: 0.5rem;"></i> DAFTAR SEKARANG
        </button>
    </div>
</form>

<div class="login-link-box">
    <p style="font-size: 0.875rem; color: #4b5563; margin-bottom: 0.75rem;">
        Sudah punya akun?
    </p>
    <a href="{{ route('login') }}">
        <i class="fas fa-sign-in-alt" style="margin-right: 0.5rem;"></i> MASUK KE AKUN
    </a>
</div>

</div>

</body>
</html>