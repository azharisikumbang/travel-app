<?php

$service = app()->getManager()->getService('MobilService');
$listRute = $service->listMobilOperasional();

response()->jsonOk(array_map(fn($item) => $item->toArray(), $listRute));