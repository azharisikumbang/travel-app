<?php

// @TODO: pindah ke middleware autorisasi
/** @var $auth User */
$auth = session()->auth();
if (!$auth) response()->unauthorized("Permintaan tidak diizinkan.");
if(false === $auth->getRole()->isAdmin()) response()->unauthorized("Permintaan tidak diizinkan.");

$service = app()->getManager()->getService('PemesananService');
$pesanan = $service->listJadwalBerdasarkan($_GET['tanggal'], $_GET['asal'], $_GET['tujuan']);

response()->jsonOk($pesanan);
