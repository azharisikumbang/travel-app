<?php

if (
    false === session()->isAuthenticatedAs('admin') ||
    request()->notPostRequest() ||
    !request()->has('id')
) response()->notFound();

$ruteService = app()->getManager()->getService('RuteService');
if(false === $ruteService->hapus($_POST['id'])) response()->badRequest(['Gagal menghapus data, mohon coba lagi.']);

response()->jsonOk(['deleted_id' => $_POST['id']]);


