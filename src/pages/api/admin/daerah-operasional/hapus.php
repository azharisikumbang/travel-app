<?php

if (false === session()->isAuthenticatedAs('admin') || request()->notPostRequest() || !isset($_POST['id'])) response()->notFound();

$daerahOperasionalService = app()->getManager()->getService('DaerahOperasionalService');
if(false === $daerahOperasionalService->hapus($_POST['id'])) response()->badRequest(['Gagal menghapus data, mohon coba lagi.']);

response()->jsonOk(['deleted_id' => $_POST['id']]);


