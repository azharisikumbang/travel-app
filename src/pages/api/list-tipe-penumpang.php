<?php

$app = app()->getManager();
$result = $app->getService('KategoriPelangganService')->listTipePenumpang();

response()->jsonOk(array_map(fn($item) => $item->toArray(), $result));