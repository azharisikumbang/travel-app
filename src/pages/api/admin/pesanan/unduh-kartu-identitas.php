<?php

if (
    false === session()->isAuthenticatedAs('admin') ||
    request()->notGetRequest() ||
    false === request()->has('file')
) response()->notFound();

$penyimpanan = app()->getManager()->getService('PenyimpananService');
$penyimpanan->downloadPhotoIdentitas($_GET['file']);

exit();


