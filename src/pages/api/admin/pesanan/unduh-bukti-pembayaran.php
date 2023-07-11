<?php

if (
    false === session()->isAuthenticatedAs('admin') ||
    request()->notGetRequest() ||
    false === request()->has(['nomor'])
) response()->notFound();

$service = app()->getManager()->getService('PemesananService');
$file = $service->unduhBuktiPembayaran($_GET['nomor']);

if (!$file) response()->badRequest(['Bukti pembayaran belum tersedia.']);

$penyimpanan = app()->getManager()->getService('PenyimpananService');
$penyimpanan->downloadBuktiPembayaran($file);


