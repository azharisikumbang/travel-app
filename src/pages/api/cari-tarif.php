<?php

$tarif = app()
    ->getManager()
    ->getService('TiketService')
    ->cariTarif($_GET['asal'], $_GET['tujuan'], $_GET['kategori']);

if (empty($tarif)) response()->jsonNotFound("RuteHarian tidak tersedia, mohon cari keberangkatan lain atau hubungi penyedia layanan.");

response()->toJson([
    'message' => 'Resources Found.', 
    'code' => 200,
    'data' => $tarif->toArray()
]);
