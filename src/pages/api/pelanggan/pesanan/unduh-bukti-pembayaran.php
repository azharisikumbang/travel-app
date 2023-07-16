<?php

if (
    false === session()->isAuthenticatedAs('pelanggan') ||
    request()->notGetRequest() ||
    false === request()->has(['nomor'])
) response()->notFound();

$service = app()->getManager()->getService('PemesananService');
$file = $service->unduhBuktiPembayaran($_GET['nomor'], session()->auth());

if (!$file) html_not_found();

$penyimpanan = app()->getManager()->getService('PenyimpananService');
$penyimpanan->downloadBuktiPembayaran($file);
