<?php

$app = app()->getManager();
$listJam = $app->getService('JamKeberangkatanService')->listJamKeberangkatan();

echo json_encode(
    array_map(fn ($item) => $item->toArray(), $listJam)
);