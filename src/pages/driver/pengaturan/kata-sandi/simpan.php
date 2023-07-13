<?php

if (
    false === session()->isAuthenticatedAs('driver') ||
    request()->notPostRequest() ||
    false === request()->has(['password', 'password_confirmation'])
) response()->notFound();

if ($_POST['password'] !== $_POST['password_confirmation'])
    response()->redirectTo(site_url('driver/pengaturan/kata-sandi'), ['status' => false, 'message' => 'Password dan password konfirmasi tidak sama, mohon periksa kembali']);

$username = session()->auth()->getUsername();
$changed = app()->getManager()->getService('AkunService')->gantiPasswordAkun($username, $_POST['password'], true);

if (false == $changed)
    response()->redirectTo(site_url('driver/pengaturan/kata-sandi'), ['status' => false, 'message' => 'Gagal mengubah pengaturan akun, mohon coba kembali']);

response()
    ->redirectTo(
        site_url('driver/pengaturan/kata-sandi'),
        ['status' => true, 'message' => 'Akun berhasil diperbaharui.']
    );