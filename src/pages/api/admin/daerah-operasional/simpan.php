<?php

if (false === session()->isAuthenticatedAs('admin') || request()->notPostRequest()) response()->notFound();

/** @var $daerah DaerahOpersional */
$app = app()->getManager();
$daerah = $app->getEntity('DaerahOpersional');
$daerah->setId($_POST['id'] ?? -1);
$daerah->setNamaKota($_POST['nama_kota']);

/** @var $daerahOperasionalService DaerahOperasionalService */
$daerahOperasionalService = $app->getService('DaerahOperasionalService');

if(false === $daerahOperasionalService->simpan($daerah)) response()->badRequest(['Gagal menyimpan data, mohon coba lagi.']);

response()->jsonOk($daerah->toArray());


