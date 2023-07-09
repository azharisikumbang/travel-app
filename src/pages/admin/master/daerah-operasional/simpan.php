<?php

/** @var $daerah DaerahOperasional */
$app = app()->getManager();
$daerah = $app->getEntity('DaerahOperasional');
$daerah->setNamaKota($_POST['nama_kota']);

/** @var $daerahOperasionalService DaerahOpersionalService */
$daerahOperasionalService = $app->getService('DaerahOperasionalService');
$daerahOperasionalService->tambahkanDaerahOperasional($daerah);

$app->getRouterManager()
    ->redirectTo(
        'admin/master/daerah-operasional',
        true,
        ['status' => true, 'message' => 'Daerah operasional berhasil ditambahkan.']
    );


