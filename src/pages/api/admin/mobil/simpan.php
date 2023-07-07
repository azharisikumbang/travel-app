<?php

if (
    false === session()->isAuthenticatedAs('admin') ||
    request()->notPostRequest() ||
    false === request()->has(['id', 'merk', 'plat_nomor', 'driver', 'jumlah_kursi'])
) response()->notFound();

$mobilService = app()->getManager()->getService('MobilService');
$mobil = $mobilService->simpan(
    $_POST['id'],
    $_POST['merk'],
    $_POST['plat_nomor'],
    $_POST['jumlah_kursi'],
    $_POST['driver']
);

if(false === $mobil) response()->badRequest(['Gagal memproses data, mohon periksa kembali data yang anda inputkan dan coba lagi.']);

response()->jsonOk($mobil->toArray());

