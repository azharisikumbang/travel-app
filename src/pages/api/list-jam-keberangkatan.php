<?php

$app = app()->getManager();
$listJam = $app->getService('JamKeberangkatanService')->listJamKeberangkatan();

response()->jsonOk(array_map(fn ($item) => $item->toArray(), $listJam));