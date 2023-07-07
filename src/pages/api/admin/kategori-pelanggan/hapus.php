<?php

if (
    false === session()->isAuthenticatedAs('admin') ||
    request()->notPostRequest() ||
    !request()->has('id')
) response()->notFound();

$service = app()->getManager()->getService('KategoriPelangganService');
if(false === $service->hapus($_POST['id'])) response()->badRequest(['Gagal menghapus data, mohon coba lagi.']);

response()->jsonOk(['deleted_id' => $_POST['id']]);


