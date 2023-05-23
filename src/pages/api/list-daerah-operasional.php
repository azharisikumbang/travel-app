<?php

$app = app()->getManager();
$result = $app->getService('DaerahOperasionalService')->listDaerahOperasional();

echo json_encode(
    array_map(fn($item) => $item->toArray(), $result)
);