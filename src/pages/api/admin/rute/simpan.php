<?php

if (
    false === session()->isAuthenticatedAs('admin') ||
    request()->notPostRequest() ||
    false === request()->has(['id', 'asal', 'tujuan'])
) response()->notFound();

$ruteService = app()->getManager()->getService('RuteService');

$rute = $ruteService->simpan($_POST['id'], $_POST['asal'], $_POST['tujuan']);

if(false === $rute) response()->badRequest(['Gagal menyimpan data, mohon coba lagi.']);

response()->jsonOk($rute->toArray());


