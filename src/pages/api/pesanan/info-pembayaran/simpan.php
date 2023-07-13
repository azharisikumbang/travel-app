<?php


if (
    false === session()->isAuthenticatedAs('pelanggan') ||
    request()->notPostRequest()
) response()->notFound();

if(!isset($_FILES['bukti'])) {
    response()->badRequest("Bukti pembayaran belum diunggah. Mohon coba kembali.");
}

$bukti = $_FILES['bukti'];

if($bukti['error']) {
    response()->badRequest("Terjadi kesalaahan pada saat unggah bukti pembayaran. Mohon coba kembali.");
}

$pesanan = session('nomor_pemesanan');
if(is_null($pesanan)) response()->badRequest("Pesanan tidak diketahui, mohon periksa kembali.");

/** @var $pemesananService PemesananService */
$pemesananService = app()->getManager()->getService('PemesananService');
$pesanan = $pemesananService->simpanBuktiPembayaran($pesanan, $_POST['nama'], $_POST['bank'], $_POST['nominal'], $bukti);

if (false === $pesanan) response()->badRequest("Gagal dalam menyimpan informasi pembayaran. Mohon coba kembali.");

response()->jsonOk([
    'nomor_pemesanan' => $pesanan->getNomorPesanan()
]);