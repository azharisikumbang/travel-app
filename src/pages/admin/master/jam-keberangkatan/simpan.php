<?php

$app = app()->getManager();
$jamKeberangkatan = $app->getEntity('JamKeberangkatan');
$jamKeberangkatan
    ->setJam($_POST['jam'])
    ->setAlias($_POST['alias']);

$service = $app->getService('JamKeberangkatanService');
$service->tambahkanJamKeberangkatan($jamKeberangkatan);

$app->getRouterManager()
    ->redirectTo(
        'admin/master/jam-keberangkatan',
        true,
        ['status' => true, 'message' => 'Jam keberangkatan telah berhasil ditambahkan.']
    );
