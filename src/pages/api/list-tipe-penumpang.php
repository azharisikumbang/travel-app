<?php

$app = app()->getManager();
$result = $app->getService('TipePenumpangService')->listTipePenumpang();

response()->jsonOk(array_map(fn($item) => $item->toArray(), $result));