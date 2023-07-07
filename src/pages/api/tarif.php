<?php

/** @var $service TarifService */
$service = app()->getManager()->getService('TarifService');

$data = null;

if(isset($_GET['tarif'])) {
    $tarif = $service->lihatDetailTarif($_GET['tarif']);
    if (!$tarif) response()->badRequest(['Tarif tidak ditemukan, mohon coba lagi.']);

    $data = $tarif->toArray();
} else {
    $data = $service->listTarifByKategori();
}

response()->jsonOk($data);

