<?php

$service = app()->getManager()->getService('RuteService');
$listRute = $service->listRuteTersedia();

response()->jsonOk(array_map(fn ($item) => $item->toArray(), $listRute));