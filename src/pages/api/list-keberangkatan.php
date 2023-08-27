<?php

/** @var $service KeberangkatanService */
$service = app()->getManager()->getService('KeberangkatanService');
$listKeberangkatan= [];

if(isset($_GET['jam']) && isset($_GET['tanggal'])) {
    $tanggalPemesanan = new DateTime($_GET['tanggal']);
    $tanggalPemesanan->setTime(date('H'), date('i'));

    $listKeberangkatan = $service->listKeberangkatanBerdasarkanJamKeberangkatan($tanggalPemesanan, $_GET['jam']);
}

response()->jsonOk(array_map(fn ($item) => $item->toArray(), $listKeberangkatan));