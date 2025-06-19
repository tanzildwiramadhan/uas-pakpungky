<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Akses tidak valid');
}

$id = intval($_POST['id']);
$url = "http://localhost:5000/pembayaran/$id";

$data = [
    'nama' => $_POST['nama'],
    'jumlah_jiwa' => $_POST['jumlah_jiwa'],
    'jenis_zakat' => $_POST['jenis_zakat'],
    'metode_pembayaran' => $_POST['metode_pembayaran'],
    'total_bayar' => $_POST['total_bayar'],
    'nominal_dibayar' => $_POST['nominal_dibayar'],
    'kembalian' => $_POST['kembalian'],
    'keterangan' => $_POST['keterangan'],
    'tanggal_bayar' => $_POST['tanggal_bayar'],
];

$payload = json_encode($data);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Content-Length: ' . strlen($payload)
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code === 200) {
    header("Location: history.php");
    exit;
} else {
    echo "Gagal update pembayaran. HTTP Code: $http_code <br>Response: $response";
}
?>