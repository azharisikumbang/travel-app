<?php

/** @var $daerah DaerahOpersional */
$app = app()->getManager();
$daerah = $app->getEntity('DaerahOpersional');
$daerah->setNamaKota($_POST['nama_kota']);

/** @var $daerahOperasionalService DaerahOpersionalService */
$daerahOperasionalService = $app->getService('DaerahOperasionalService');
$daerahOperasionalService->tambahkanDaerahOperasional($daerah);

$app->getRouterManager()
    ->redirectTo(
        'admin/master/wilayah-operasional',
        true,
        ['status' => true, 'message' => 'Wilayah operasional berhasil ditambahkan.']
    );


