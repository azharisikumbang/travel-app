<?php

$tarif = app()
    ->getManager()
    ->getService('TarifService')
    ->cariTarif($_GET['asal'], $_GET['tujuan'], $_GET['kategori']);

if (empty($tarif)) response()->jsonNotFound("Rute tidak tersedia, mohon cari rute lain atau hubungi penyedia layanan.");

response()->toJson([
    'message' => 'Resources Found.', 
    'code' => 200,
    'data' => $tarif->toArray()
]);
