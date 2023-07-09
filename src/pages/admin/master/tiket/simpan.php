<?php

$app = app()->getManager();
$tarifService = $app->getService('TiketService');
$tarif = $tarifService->buatTarif(
    $_POST['tiket'],
    $_POST['kategori'],
    $_POST['asal'],
    $_POST['tujuan'],
);


$isSaved = $tarifService->simpanTarifBaru($tarif);

if($isSaved) return $app
    ->getRouterManager()
    ->redirectTo(
    'admin/master/tiket',
    true,
    ['status' => true, 'message' => 'Data tiket baru berhasil ditambahkan.']
);
