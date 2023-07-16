<?php

if (
    false === session()->isAuthenticatedAs('pelanggan') ||
    request()->notGetRequest()
) response()->notFound();

$service = app()->getManager()->getService('PelangganService');
$pelanggan = $service->informasiSaya(session()->auth());

if (!$pelanggan) response()->badRequest(['File tidak tersedia.']);

if (is_null($pelanggan->getPhotoIdentitas())) response()->badRequest(['File tidak tersedia.']);


$penyimpanan = app()->getManager()->getService('PenyimpananService');
$penyimpanan->downloadPhotoIdentitas($pelanggan->getPhotoIdentitas());

exit();


