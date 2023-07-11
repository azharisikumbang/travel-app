<?php

if (session()->auth()) response()->redirectTo(site_url(session()->auth()->getRole()->redirectPage()));

if (strtolower($_SERVER['REQUEST_METHOD']) !== 'post') response()->notFound();
if ($_POST['password'] !== $_POST['confirm_password']) response()->redirectTo(site_url('register'), ['status' => false, 'message' => 'Password konfirmasi tidak sama, mohon periksa kembali']);


/** @var $service PelangganService */
$service = app()->getManager()->getService('PelangganService');
$user = $service->buatAkunPelanggan(
    $_POST['nama'],
    $_POST['kontak'],
    $_POST['username'],
    $_POST['password'],
);

if (false === $user) response()->redirectTo(site_url('akun/register'), ['status' => false, 'message' => 'Username telah digunakan, mohon coba lagi.']);

response()->redirectTo(site_url('akun/login'), ['status' => true, 'message' => 'Akun berhasil dibuat. Silahkan masuk.']);
