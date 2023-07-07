<?php

/** @var $tipePenumpang KategoriPelanggan */
$app = app()->getManager();
$tipePenumpang = $app->getEntity('KategoriPelanggan');
$tipePenumpang->setTipePenumpang($_POST['kategori_penumpang']);

/** @var $service KategoriPelangganService */
$service = $app->getService('KategoriPelangganService');
$service->tambahkanTipePenumpang($tipePenumpang);

$app->getRouterManager()
    ->redirectTo(
        'admin/master/kategori-penumpang',
        true,
        ['status' => true, 'message' => 'Kategori penumpang telah berhasil ditambahkan.']
    );


