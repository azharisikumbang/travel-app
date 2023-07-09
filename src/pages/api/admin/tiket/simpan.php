<?php

if (
    false === session()->isAuthenticatedAs('admin') ||
    request()->notPostRequest() ||
    false === request()->has(['id', 'tarif', 'kategori', 'rute'])
) response()->notFound();

$service = app()->getManager()->getService('TiketService');
$result = $service->simpan(
    $_POST['id'],
    $_POST['rute'],
    $_POST['kategori'],
    $_POST['tarif']
);

if(false === $result) response()->badRequest(['Gagal memproses data, mohon periksa kembali data yang anda inputkan dan coba lagi.']);

response()->jsonOk($result->toArray());

