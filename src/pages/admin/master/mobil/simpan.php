<?php

$app = app()->getManager();
$mobil = $app->getEntity('Mobil');
$mobil
    ->setMerk($_POST['merk'])
    ->setNomorPolisi($_POST['nomor_polisi'])
    ->setJumlahKursi($_POST['jumlah_kursi']);

$service = $app->getService('MobilService');
$service->tambahkanMobilOperasional($mobil);

$app->getRouterManager()
    ->redirectTo(
        'admin/master/mobil',
        true,
        ['status' => true, 'message' => 'Data mobil operasional berhasil ditambahkan.']
    );
