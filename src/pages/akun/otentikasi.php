<?php

if(!request()->isPostRequest()) return response()->notFound();

$username = $_POST['username'];
$password = $_POST['password'];

/** @var $otentikator OtentikatorService */
$otentikator = app()->getManager()->getService('OtentikatorService');

if (false === $otentikator->otentikasi($username, $password))
    response()->redirectTo(site_url('akun/login'), ['status' => false, 'message' => 'Username atau password salah, silahkan coba kembali.']);

/** @var $user Akun */
$user = session()->auth();
response()->redirectTo(site_url($user->getRole()->redirectPage()));
