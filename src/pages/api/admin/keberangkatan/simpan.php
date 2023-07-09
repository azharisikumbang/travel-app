<?php

if (
    false === session()->isAuthenticatedAs('admin') ||
    request()->notPostRequest() ||
    false === request()->has(['jam_keberangkatan', 'mobil', 'rute'])
) response()->notFound();

$rute = is_string($_POST['rute']) ? explode(',', $_POST['rute']) : $_POST['rute'];

$service = app()->getManager()->getService('KeberangkatanService');
$result = $service->simpan(
    $_POST['mobil'],
    $_POST['jam_keberangkatan'],
    $rute
);

if(false === $result) response()->badRequest(['Gagal memproses data, mohon periksa kembali data yang anda inputkan dan coba lagi.']);

response()->jsonOk(['success' => true]);