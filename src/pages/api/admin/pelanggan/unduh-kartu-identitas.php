<?php

if (
    false === session()->isAuthenticatedAs('admin') ||
    request()->notGetRequest() ||
    false === request()->has('photo')
) response()->notFound();

$penyimpanan = app()->getManager()->getService('PenyimpananService');
$penyimpanan->downloadPhotoIdentitas($_GET['photo']);

exit();


