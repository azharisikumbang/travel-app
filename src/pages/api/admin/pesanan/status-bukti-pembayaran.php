<?php

// @TODO: pindah ke middleware autorisasi
/** @var $auth User */
$auth = session()->auth();
if (!$auth) response()->unauthorized("Permintaan tidak diizinkan.");
if(false === $auth->getRole()->isAdmin()) response()->unauthorized("Permintaan tidak diizinkan.");

if(!isset($_GET['status']) || !isset($_GET['nomor'])) response()->badRequest('Terjadi kesalahan mohon periksa pesanan anda.');

$status = $_GET['status'];
$nomor = $_GET['nomor'];

/** @var $service PemesananService */
$service = app()->getManager()->getService('PemesananService');

/** @var $pesanan Pesanan */
$pesanan = $service->cariPesananBerdasarkanNomorPesanan($nomor);

if(is_null($pesanan)) response()->badRequest('Nomor tiket pesanan tidak ditemukan mohon periksa kembali.');

$status = match ((int) $status) {
    -1 => 'INVALID',
    1 => 'VALID',
    default => $pesanan->getStatusBuktiPembayaran()->value
};

$confirmed = $service->konfirmasiPembayaran($pesanan, $status);

if (false === $confirmed) response()->badRequest('Gagal mengkonfirmasi pembayaran, mohon coba lagi.');

response()->toJson([
    'nomor_pemesanan' => $confirmed->getNomorPesanan(),
    'status_konfirmasi' => $confirmed->getStatusBuktiPembayaran()->value
]);