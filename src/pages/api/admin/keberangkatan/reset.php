<?php

if (
    false === session()->isAuthenticatedAs('admin') ||
    request()->notPostRequest() ||
    !request()->has('mobil')
) response()->notFound();

$service = app()->getManager()->getService('KeberangkatanService');
if(false === $service->resetRuteMobil($_POST['mobil'])) response()->badRequest(['Gagal menghapus data, mohon coba lagi.']);

response()->jsonOk(['deleted_id' => $_POST['mobil']]);