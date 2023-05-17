<?php

$app = app()->getManager();
$tarifService = $app->getService('TarifService');
$tarif = $tarifService->buatTarif(
    $_POST['tarif'],
    $_POST['kategori'],
    $_POST['asal'],
    $_POST['tujuan'],
);


$isSaved = $tarifService->simpanTarifBaru($tarif);

if($isSaved) return $app
    ->getRouterManager()
    ->redirectTo(
    'admin/master/tarif',
    true,
    ['status' => true, 'message' => 'Data tarif baru berhasil ditambahkan.']
);
