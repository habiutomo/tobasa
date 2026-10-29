<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Antrian Puskesmas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0d6efd 0%, #0a4fb0 100%);
        }
        .login-card {
            max-width: 420px;
            width: 100%;
            border: 0;
            border-radius: 1rem;
            box-shadow: 0 20px 40px rgba(0,0,0,.2);
        }
        .login-logo {
            width: 70px; height: 70px;
            background: #0d6efd;
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin: 0 auto 1rem;
        }
    </style>
</head>
<body>
    <div class="card login-card p-4">
        <div class="login-logo"><i class="bi bi-hospital"></i></div>
        <h1 class="h4 text-center mb-1">Antrian Puskesmas</h1>
        <p class="text-center text-muted small mb-4">Silakan masuk dengan akun petugas</p>

        @if($errors->any())
            <div class="alert alert-danger py-2 small">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="form-control" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" name="remember" id="remember" class="form-check-input">
                <label for="remember" class="form-check-label">Ingat saya</label>
            </div>
            <button class="btn btn-primary w-100">Masuk</button>
        </form>

        <div class="mt-3 text-center small">
            <a href="{{ route('display.index') }}" class="text-decoration-none"><i class="bi bi-tv"></i> Buka Layar Antrian</a>
        </div>
    </div>
</body>
</html>