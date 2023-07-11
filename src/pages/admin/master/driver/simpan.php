<?php

if (false === session()->isAuthenticatedAs('admin')) html_unauthorized();

/** @var $user Akun */
$app = app()->getManager();
$user = $app->getEntity('Akun');
$user
    ->setNamaLengkap($_POST['nama_lengkap'])
    ->setKontak($_POST['kontak'])
    ->setUsername($_POST['username'])
    ->setPassword($_POST['password'])
    ->setRole('driver')
;

$userRepository = $app->getRepository('UserRepository');

$userService = $app->getService('UserService');
$userService->tambahkanAkunOperasional($user, $userRepository);

$app->getRouterManager()
    ->redirectTo(
        'admin/master/driver',
        true,
        ['status' => true, 'message' => 'Akun berhasil ditambahkan.']
    );


