<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Kuni's</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #eef2f7;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        .saldo-card {
            background: linear-gradient(135deg, #ffc107, #ff9800);
            color: white;
            border: none;
        }
        @media print {
            #print-button {
                display: none;
            }
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="text-center mb-4">
        <h2 class="fw-bold">Kuni's wallet</h2>
    </div>

    <!-- Saldo Siswa -->
    <div class="card saldo-card text-center p-4 mb-4">
        <h4>Saldo Anda</h4>
        <h2>Rp {{ number_format(optional(auth()->user()->wallet)->balance ?? 0, 0, ',', '.') }}</h2>
    </div>

    <div class="row g-4">
        <!-- Form Top-Up Saldo -->
        <div class="col-md-6">
            <div class="card p-4">
                <h5 class="mb-3">Isi Saldo</h5>
                <form action="{{ route('siswa.topup') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Jumlah Top-Up:</label>
                        <input type="number" name="amount" class="form-control" placeholder="Masukkan jumlah" required>
                    </div>
                    <button type="submit" class="btn btn-warning w-100">Ajukan Top-Up</button>
                </form>
            </div>
        </div>

        <!-- Form Transfer -->
        <div class="col-md-6">
            <div class="card p-4">
                <h5 class="mb-3">Transfer ke Siswa Lain</h5>
                <form action="{{ route('siswa.transfer') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Pilih Siswa:</label>
                        <select name="receiver_id" class="form-select">
                            @foreach($siswa ?? [] as $s)
                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jumlah Transfer:</label>
                        <input type="number" name="amount" class="form-control" placeholder="Masukkan jumlah" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Transfer</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Riwayat Transaksi -->
    <div class="card p-4 mt-4">
        <h5>Riwayat Transaksi Nasabah</h5>
        <div class="table-responsive">
            <table class="table table-striped mt-3">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tipe</th>
                        <th>Jumlah</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(auth()->user()->transactions ?? [] as $transaction)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ ucfirst($transaction->type) }}</td>
                            <td>Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                            <td>{{ $transaction->created_at->format('d M Y') }}</td>
                            <td><button id="print-button" class="btn btn-info btn-sm" onclick="printReceipt('{{ ucfirst($transaction->type) }}', '{{ number_format($transaction->amount, 0, ',', '.') }}', '{{ $transaction->created_at->format('d M Y H:i') }}')">Cetak</button></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- JavaScript untuk Cetak Bukti -->
<script>
    function printReceipt(type, amount, date) {
        let printWindow = window.open('', '', 'width=600,height=400');
        printWindow.document.write('<html><head><title>Bukti Transaksi</title>');
        printWindow.document.write('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">');
        printWindow.document.write('<style>@media print { #print-button { display: none; } }</style>');
        printWindow.document.write('</head><body class="p-4">');
        printWindow.document.write('<div class="text-center">');
        printWindow.document.write('<h2>Bukti Transaksi</h2>');
        printWindow.document.write('<hr>');
        printWindow.document.write('<p><strong>Tipe:</strong> ' + type + '</p>');
        printWindow.document.write('<p><strong>Jumlah:</strong> Rp ' + amount + '</p>');
        printWindow.document.write('<p><strong>Tanggal:</strong> ' + date + '</p>');
        printWindow.document.write('<button id="print-button" class="btn btn-primary" onclick="window.print()">Cetak</button>');
        printWindow.document.write('</div>');
        printWindow.document.write('</body></html>');
        printWindow.document.close();
    }
</script>
</body>
</html>
