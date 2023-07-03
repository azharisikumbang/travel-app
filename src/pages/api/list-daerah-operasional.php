<?php

$app = app()->getManager();
$result = $app->getService('DaerahOperasionalService')->listDaerahOperasional();

response()->jsonOk(array_map(fn($item) => $item->toArray(), $result));