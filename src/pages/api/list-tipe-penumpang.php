<?php

$app = app()->getManager();
$result = $app->getService('TipePenumpangService')->listTipePenumpang();

echo json_encode(
    array_map(fn($item) => $item->toArray(), $result)
);