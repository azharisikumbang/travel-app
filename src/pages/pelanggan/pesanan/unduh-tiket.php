<?php

if (
    false === session()->isAuthenticatedAs('pelanggan') ||
    request()->notGetRequest() ||
    false === request()->has(['nomor'])
) response()->notFound();

$service = app()->getManager()->getService('PemesananService');
$file = $service->unduhTiket($_GET['nomor'], session()->auth());

if (!$file) response()->badRequest(['Tiket belum tersedia.']);

$penyimpanan = app()->getManager()->getService('PenyimpananService');
$penyimpanan->downloadTiket($file);

exit();


