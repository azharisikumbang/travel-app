<?php

if (
    false === session()->isAuthenticatedAs('admin') ||
    request()->notPostRequest() ||
    false === request()->has('nomor')
) response()->notFound();

$nomor = $_GET['nomor'];

/** @var $service PemesananService */
/** @var $pesanan Pesanan */
$service = app()->getManager()->getService('PemesananService');
$pesanan = $service->cariPesananBerdasarkanNomorPesanan($nomor);
$success = $service->batalkanPesanan($pesanan);

if (false === $success) response()->badRequest('Gagal membatalkan pesanan, mohon coba kembali');

response()->jsonOk([
    'status' => 'SUCCESS',
    'message' => 'Pesanan berhasil dibatalkan.',
    'query' => [
        'nomor_pesanan' => $nomor
    ]
]);