<?php
date_default_timezone_set('Asia/Jakarta');
ob_start();

// Ambil data pembayaran dari API
$pembayaran_data = [];
$pembayaran_error = null;
$api_url_pembayaran = 'http://localhost:5000/pembayaran';
$ch = curl_init($api_url_pembayaran);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code === 200) {
    $pembayaran_data = json_decode($response, true);
    // Urutkan data berdasarkan tanggal_bayar secara descending
    usort($pembayaran_data, function($a, $b) {
        return strtotime($b['tanggal_bayar']) - strtotime($a['tanggal_bayar']);
    });
} else {
    $pembayaran_error = 'Gagal mengambil data: HTTP ' . $http_code;
}

$total_pembayaran = 0;
$jumlah_transaksi = count($pembayaran_data);
$tanggal_terbaru = '-';

if ($jumlah_transaksi > 0) {
    foreach ($pembayaran_data as $item) {
        $total_pembayaran += floatval($item['total_bayar']);
    }
    $tanggal_terbaru = $pembayaran_data[0]['tanggal_bayar']; // Ambil tanggal terbaru setelah diurutkan
}
?>

<body class="bg-gray-50 text-gray-700 font-sans">
    <div class="mx-auto p-6">
        <h1 class="text-3xl mb-4 text-center">Sistem Pembayaran Zakat</h1>

        <?php if ($pembayaran_error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <?= htmlspecialchars($pembayaran_error); ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Pembayaran</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">Rp.<?= number_format($total_pembayaran) ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-money-bills"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Jumlah Transaksi</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $jumlah_transaksi ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Tanggal Terbaru</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $tanggal_terbaru ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Data Pembayaran</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Jenis Zakat</th>
                                <th>Total Bayar (Rp)</th>
                                <th>Tanggal Bayar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($pembayaran_data, 0, 5) as $data): ?>
                                <tr class="hover:bg-gray-50 border-b">
                                    <td class="px-4 py-2"><?= htmlspecialchars($data['nama']); ?></td>
                                    <td class="px-4 py-2"><?= htmlspecialchars($data['jenis_zakat']); ?></td>
                                    <td class="px-4 py-2 text-right"><?= number_format($data['total_bayar']); ?></td>
                                    <td class="px-4 py-2"><?= htmlspecialchars($data['tanggal_bayar']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        

        <!-- Navigasi -->
        <div class="text-center">
            <a href="pembayaran.php">
                <button type="button" class="btn btn-primary">
                    Tambah Pembayaran
                </button>
            </a>
            <a href="history.php">
                <button type="button" class="btn btn-success">
                    History Pembayaran
                </button>
            </a>
            <a href="beras.php">
                <button type="button" class="btn btn-info">
                    Data Beras
                </button>
            </a>
        </div>
    </div>
</body>

<?php 
$content = ob_get_clean();
require_once 'layout.php'
?>