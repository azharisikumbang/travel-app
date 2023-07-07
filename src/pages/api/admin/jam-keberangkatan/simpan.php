<?php

if (
    false === session()->isAuthenticatedAs('admin') ||
    request()->notPostRequest() ||
    false === request()->has(['id', 'jam', 'alias'])
) response()->notFound();

$service = app()->getManager()->getService('JamKeberangkatanService');
$result = $service->simpan(
    $_POST['id'],
    $_POST['jam'],
    $_POST['alias']
);

if(false === $result) response()->badRequest(['Gagal memproses data, mohon periksa kembali data yang anda inputkan dan coba lagi.']);

response()->jsonOk($result->toArray());

