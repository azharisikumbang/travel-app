<?php

if (
    false === session()->isAuthenticatedAs('admin') ||
    request()->notPostRequest() ||
    false === request()->has(['id', 'nama', 'kontak', 'akun', 'username', 'password'])
) response()->notFound();

/** @var $akunService AkunService */
$akunService = app()->getManager()->getService('AkunService');
$akun = $akunService->usernameTerdaftar($_POST['username']);

$service = app()->getManager()->getService('DriverService');

if (is_null($akun)) {
    $akun = $akunService->tambahkanAkunDriver($_POST['username'], $_POST['password']);
    $driver = $service->tambahkanDriver($_POST['nama'], $_POST['kontak'], null, $akun);

    response()->jsonOk($driver->toArray());
}

$rute = $service->updateInformasiDriver(
    $_POST['id'],
    $_POST['nama'],
    $_POST['kontak'],
    $akun
);

if(false === $rute) response()->badRequest(['Gagal menyimpan data, mohon coba lagi.']);

response()->jsonOk($rute->toArray());


