<?php

if (
    false === session()->isAuthenticatedAs('pelanggan') ||
    request()->notGetRequest() ||
    false === request()->has(['nomor'])
) html_not_found();

$service = app()->getManager()->getService('PemesananService');
$file = $service->unduhTiket($_GET['nomor'], session()->auth());

if (!$file) html_not_found();

$penyimpanan = app()->getManager()->getService('PenyimpananService');
$penyimpanan->downloadTiket($file);

exit();


