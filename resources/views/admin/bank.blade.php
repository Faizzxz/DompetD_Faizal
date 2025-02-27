<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bank Kuni's</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .table thead {
            background-color: #007bff;
            color: white;
        }
        .btn-success, .btn-primary {
            border-radius: 20px;
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="text-center mb-4">
        <h2 class="fw-bold text-primary">Dashboard Bank</h2>
    </div>
    
    <!-- Form Tambah Akun Siswa -->
    <div class="card p-4 mb-4">
        <h5 class="text-center text-primary">Tambah Akun Siswa</h5>
        <form action="{{ route('bank.addStudent') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Nama Siswa:</label>
                <input type="text" name="name" class="form-control" placeholder="Masukkan nama" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email:</label>
                <input type="email" name="email" class="form-control" placeholder="Masukkan email" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password:</label>
                <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Tambah Siswa</button>
        </form>
    </div>
    
    <div class="card p-4">
        <h5 class="text-center text-primary">Konfirmasi Top-Up</h5>
        <table class="table table-hover text-center">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Siswa</th>
                    <th>Jumlah</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach(App\Models\TopUpRequest::where('status', 'pending')->get() as $topup)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $topup->user->name }}</td>
                        <td>Rp {{ number_format($topup->amount, 0, ',', '.') }}</td>
                        <td>
                            <span class="badge bg-warning text-dark">{{ ucfirst($topup->status) }}</span>
                        </td>
                        <td>
                            <form action="{{ route('bank.confirmTopUp', $topup->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm">Konfirmasi</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
