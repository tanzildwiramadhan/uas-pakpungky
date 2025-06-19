<?php 
ob_start();
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

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $url = "http://localhost:5000/pembayaran/" . $id;

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code === 200) {
        header("Location: index.php?success=1");
        exit;
    } else {
        echo "Gagal menghapus pembayaran. HTTP Code: $http_code <br>Response: $response";
    }
}

?>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <div class="d-flex justify-content-between items-center">
        <h6 class="m-0 font-weight-bold text-primary">Data Histori Pembayaran</h6>
        <a href="index.php" style="background-color: #52525b" class="btn btn-secondary btn-sm" >Kembali</a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Jumlah Jiwa</th>
                        <th>Jenis Zakat</th>
                        <th>Metode Pembayaran</th>
                        <th>Total Bayar</th>
                        <th>Nominal Bayar</th>
                        <th>Kembalian</th>
                        <th>Keterangan</th>
                        <th>Tanggal Bayar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (array_slice($pembayaran_data, 0, 5) as $data): ?>
                        <tr class="hover:bg-gray-50 border-b">
                            <td class="px-4 py-2"><?= htmlspecialchars($data['nama']); ?></td>
                            <td class="px-4 py-2"><?= $data['jumlah_jiwa']; ?></td>
                            <td class="px-4 py-2 text-right"><?= number_format($data['total_bayar']); ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($data['metode_pembayaran']); ?></td>
                            <td class="px-4 py-2"><?= number_format($data['total_bayar']); ?></td>
                            <td class="px-4 py-2"><?= number_format($data['nominal_dibayar']); ?></td>
                            <td class="px-4 py-2"><?= number_format($data['kembalian']); ?></td>
                            <td class="px-4 py-2"><?= $data['keterangan'] ?></td>
                            <td class="px-4 py-2"><?= $data['tanggal_bayar'] ?></td>
                            <td class="row">
                                <button 
                                    class="col btn btn-warning btn-sm"
                                    data-toggle="modal" 
                                    data-target="#editModal"
                                    data-id="<?= $data['id']; ?>"
                                    data-nama="<?= htmlspecialchars($data['nama']); ?>"
                                    data-jumlah="<?= $data['jumlah_jiwa']; ?>"
                                    data-jenis="<?= htmlspecialchars($data['jenis_zakat']); ?>"
                                    data-metode="<?= htmlspecialchars($data['metode_pembayaran']); ?>"
                                    data-total="<?= $data['total_bayar']; ?>"
                                    data-nominal="<?= $data['nominal_dibayar']; ?>"
                                    data-kembalian="<?= $data['kembalian']; ?>"
                                    data-keterangan="<?= htmlspecialchars($data['keterangan']); ?>"
                                    data-tanggal="<?= $data['tanggal_bayar']; ?>"
                                >
                                    Edit
                                </button>
                                <a href="history.php?id=<?= $data['id']; ?>" class="col btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus data ini?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <form method="POST" action="update_pembayaran.php">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="editModalLabel">Edit Pembayaran</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body row">
              <input type="hidden" name="id" id="edit-id">

              <div class="form-group col-md-6">
                  <label>Nama</label>
                  <input type="text" name="nama" id="edit-nama" class="form-control" required>
              </div>

              <div class="form-group col-md-6">
                  <label>Jumlah Jiwa</label>
                  <input type="number" name="jumlah_jiwa" id="edit-jumlah" class="form-control" required>
              </div>

              <div class="form-group col-md-6">
                  <label>Jenis Zakat</label>
                  <input type="text" name="jenis_zakat" id="edit-jenis" class="form-control" required>
              </div>

              <div class="form-group col-md-6">
                  <label>Metode Pembayaran</label>
                  <input type="text" name="metode_pembayaran" id="edit-metode" class="form-control" required>
              </div>

              <div class="form-group col-md-6">
                  <label>Total Bayar</label>
                  <input type="number" name="total_bayar" id="edit-total" class="form-control" required>
              </div>

              <div class="form-group col-md-6">
                  <label>Nominal Dibayar</label>
                  <input type="number" name="nominal_dibayar" id="edit-nominal" class="form-control" required>
              </div>

              <div class="form-group col-md-6">
                  <label>Kembalian</label>
                  <input type="number" name="kembalian" id="edit-kembalian" class="form-control" required>
              </div>

              <div class="form-group col-md-6">
                  <label>Keterangan</label>
                  <input type="text" name="keterangan" id="edit-keterangan" class="form-control">
              </div>

              <div class="form-group col-md-6">
                  <label>Tanggal Bayar</label>
                  <input type="date" name="tanggal_bayar" id="edit-tanggal" class="form-control" required>
              </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
          </div>
        </div>
    </form>
  </div>
</div>

<script>
$('#editModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);

    // Ambil data dari atribut tombol
    $('#edit-id').val(button.data('id'));
    $('#edit-nama').val(button.data('nama'));
    $('#edit-jumlah').val(button.data('jumlah'));
    $('#edit-jenis').val(button.data('jenis'));
    $('#edit-metode').val(button.data('metode'));
    $('#edit-total').val(button.data('total'));
    $('#edit-nominal').val(button.data('nominal'));
    $('#edit-kembalian').val(button.data('kembalian'));
    $('#edit-keterangan').val(button.data('keterangan'));
    $('#edit-tanggal').val(button.data('tanggal'));
});
</script>

<?php 
$content = ob_get_clean();
require_once 'layout.php';
?>