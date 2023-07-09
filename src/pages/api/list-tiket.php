<?php

$app = app()->getManager();
$listTarifByKategori = $app->getService('TiketService')->listTarifByKategori(100);

echo json_encode(array_values($listTarifByKategori));