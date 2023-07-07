<?php

if (
    false === session()->isAuthenticatedAs('admin') ||
    request()->notPostRequest() ||
    !request()->has('id')
) response()->notFound();

$driverService = app()->getManager()->getService('DriverService');
if(false === $driverService->hapus($_POST['id'])) response()->badRequest(['Gagal menghapus data, mohon coba lagi.']);

response()->jsonOk(['deleted_id' => $_POST['id']]);


