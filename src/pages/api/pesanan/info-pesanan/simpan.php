<?php

$pemesananService = app()->getManager()->getService('PemesananService');

/** @var bool|$pesanan Pesanan */
$pesanan = session('nomor_pemesanan');
$pesanan = $pemesananService->simpanInformasiPemesan($pesanan, $_POST['nama'], $_POST['kontak'], $_POST['titik_jemput']);

if(false === $pesanan) response()->badRequest("Pesanan tidak diketahui, mohon cek kembali pesanan anda.");

session()->add('pesanan', $pesanan->toArray());
response()->jsonOk($pesanan->toArray(), 204, "Resources Updated.");