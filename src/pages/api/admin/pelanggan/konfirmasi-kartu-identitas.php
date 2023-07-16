<?php

if (
    false === session()->isAuthenticatedAs('admin') ||
    request()->notPostRequest() ||
    false === request()->has('id')
) response()->notFound();

$pelangganService = app()->getManager()->getService('PelangganService');
$confirmed = $pelangganService->konfirmasiKartuIdentitas($_POST['id']);

if(!$confirmed) response()->badRequest(['Gagal mengkonfirmasi.']);

response()->jsonOk(['Terkonfirmasi']);

exit();


