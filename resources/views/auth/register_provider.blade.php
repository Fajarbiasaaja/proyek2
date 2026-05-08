<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Penyedia Jasa - JASAKU</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { background: #f4f6f8; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; padding: 30px; }
        .card { max-width: 680px; margin: 0 auto; border-radius: 10px; box-shadow: 0 8px 30px rgba(28,38,51,0.08); }
        .form-label { font-weight: 600; }
        .btn-primary { background: #0d6efd; border: none; }
    </style>
</head>
<body>
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Daftar sebagai Penyedia Jasa / Teknisi</h4>

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('auth.register.provider') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nomor Telepon</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Alamat</label>
                    <textarea name="address" class="form-control" rows="2" required>{{ old('address') }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Spesialisasi (opsional)</label>
                    <input type="text" name="specialization" value="{{ old('specialization') }}" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>

                <div class="d-grid gap-2">
                    <button class="btn btn-primary" type="submit"><i class="bi bi-person-plus"></i> Daftar Penyedia</button>
                    <a href="{{ route('login') }}" class="btn btn-outline-secondary">Kembali ke Login</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
