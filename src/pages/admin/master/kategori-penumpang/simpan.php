<?php

/** @var $tipePenumpang TipePenumpang */
$app = app()->getManager();
$tipePenumpang = $app->getEntity('TipePenumpang');
$tipePenumpang->setTipePenumpang($_POST['kategori_penumpang']);

/** @var $tipePenumpangService TipePenumpangService */
$tipePenumpangService = $app->getService('TipePenumpangService');
$tipePenumpangService->tambahkanTipePenumpang($tipePenumpang);

$app->getRouterManager()
    ->redirectTo(
        'admin/master/kategori-penumpang',
        true,
        ['status' => true, 'message' => 'Kategori penumpang telah berhasil ditambahkan.']
    );


