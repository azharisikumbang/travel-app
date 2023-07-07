<?php

$app = app()->getManager();
$listTarifByKategori = $app->getService('TarifService')->listTarifByKategori(100);

echo json_encode(array_values($listTarifByKategori));