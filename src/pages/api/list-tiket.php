<?php

$app = app()->getManager();
$listTarifByKategori = $app->getService('TarifService')->listTarifByKategori();

echo json_encode(array_values($listTarifByKategori));