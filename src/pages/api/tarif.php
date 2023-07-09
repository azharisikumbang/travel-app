<?php

/** @var $service TiketService */
$service = app()->getManager()->getService('TiketService');

$data = null;

if(isset($_GET['tiket'])) {
    $tarif = $service->lihatDetailTarif($_GET['tiket']);
    if (!$tarif) response()->badRequest(['Tiket tidak ditemukan, mohon coba lagi.']);

    $data = $tarif->toArray();
} else {
    $data = $service->listTarifByKategori();
}

response()->jsonOk($data);

