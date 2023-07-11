<?php

if (false === session()->isAuthenticatedAs('admin')) html_unauthorized();

$app = app()->getManager();
$mobil = $app->getEntity('Mobil');
$mobil
    ->setMerk($_POST['merk'])
    ->setNomorPolisi($_POST['nomor_polisi'])
    ->setJumlahKursi($_POST['jumlah_kursi']);

$service = $app->getService('MobilService');
$saved = $service->tambahkanMobilOperasional($mobil, $_POST['driver']);

if(false === $saved)
    $app->getRouterManager()->redirectTo(
        'admin/master/mobil',
        true,
        ['status' => false, 'message' => 'Data mobil gagal ditambahkan.']
    );

$app->getRouterManager()
    ->redirectTo(
        'admin/master/mobil',
        true,
        ['status' => true, 'message' => 'Data mobil operasional berhasil ditambahkan.']
    );
