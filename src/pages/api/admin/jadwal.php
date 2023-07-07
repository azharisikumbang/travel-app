<?php

// @TODO: pindah ke middleware autorisasi
/** @var $auth Akun */
$auth = session()->auth();
if (!$auth) response()->unauthorized("Permintaan tidak diizinkan.");
if(false === $auth->getRole()->isAdmin()) response()->unauthorized("Permintaan tidak diizinkan.");

/** @var $service PemesananService */
$service = app()->getManager()->getService('PemesananService');
/** @var $pesanan Pesanan */
$pesanan = $service->listJadwalBerdasarkan($_GET['tanggal'], $_GET['asal'], $_GET['tujuan']);

response()->jsonOk($pesanan);
