<?php

// @TODO: pindah ke middleware autorisasi
/** @var $auth Akun */
$auth = session()->auth();
if (!$auth) response()->unauthorized("Permintaan tidak diizinkan.");
if(false === $auth->getRole()->isAdmin()) response()->unauthorized("Permintaan tidak diizinkan.");

//sleep(30);

$service = app()->getManager()->getService('RuteService');
if (!$service->updateRuteHarian($_POST['keberangkatan'], $_POST['mobil'], $_POST['jam'])) response()->badRequest('Gagal merubah detail keberangkatan, mohon coba lagi.');

response()->jsonOk([
    'keberangkatan' => $_POST['keberangkatan'],
    'last_updated' => tanggal(date_create())
]);
