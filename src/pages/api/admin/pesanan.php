<?php

// @TODO: pindah ke middleware autorisasi
/** @var $auth User */
$auth = session()->auth();
if (!$auth) response()->unauthorized("Permintaan tidak diizinkan.");
if(false === $auth->getRole()->isAdmin()) response()->unauthorized("Permintaan tidak diizinkan.");

$service = app()->getManager()->getService('PemesananService');

if (isset($_GET['tanggal'])) $pesanan = $service->listPesananBerdasarkanTanggalKeberangkatan(date_create($_GET['tanggal']));
if (isset($_GET['nomor'])) {
    $pesanan = $service->listPesananBerdasarkanNomorPesanan($_GET['nomor']);

    if (!empty($pesanan)) $pesanan = [$pesanan];
}

response()->jsonOk($pesanan);
