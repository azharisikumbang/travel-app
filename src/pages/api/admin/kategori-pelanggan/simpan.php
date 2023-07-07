<?php

if (
    false === session()->isAuthenticatedAs('admin') ||
    request()->notPostRequest() ||
    false === request()->has(['id', 'kategori'])
) response()->notFound();

$service = app()->getManager()->getService('KategoriPelangganService');
$result = $service->simpan(
    $_POST['id'],
    $_POST['kategori']
);

if(false === $result) response()->badRequest(['Gagal memproses data, mohon periksa kembali data yang anda inputkan dan coba lagi.']);

response()->jsonOk($result->toArray());

