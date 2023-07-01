<?php

// validasi tanggal pemesanan
$tanggalPemesanan = date_create($_GET['tanggal']);
$tiketService = app()->getManager()->getService('TiketService');
if (false === $tiketService->cekTanggalKeberangkatanValid($tanggalPemesanan)) {
    response()->badRequest("Tanggal yang dipesan sudah terlewat, mohon periksa kembali.");
}

// validasi rute
$tarifService = app()->getManager()->getService('TarifService');
$tarifEntity = $tarifService->cariTarif($_GET['asal'], $_GET['tujuan'], $_GET['kategori']);
if (null === $tarifEntity) {
    response()->badRequest("Rute dan kategori dipilih tidak tersedia, mohon periksa kembali.");
}

$listKursiTersedia = $tiketService->cariKursiTersedia(
    date_create($_GET['tanggal']),
    $_GET['jam'],
    $tarifEntity
);

response()->jsonOk(
    $listKursiTersedia
);