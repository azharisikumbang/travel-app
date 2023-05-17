<?php

/** @var $user User */
$app = app()->getManager();
$user = $app->getEntity('User');
$user
    ->setNamaLengkap($_POST['nama_lengkap'])
    ->setKontak($_POST['kontak'])
    ->setUsername($_POST['username'])
    ->setPassword($_POST['password'])
    ->setRole($_POST['role'])
;

$userRepository = $app->getRepository('UserRepository');

$userService = $app->getService('UserService');
$userService->tambahkanAkunOperasional($user, $userRepository);

$app->getRouterManager()
    ->redirectTo(
        'admin/master/akun',
        true,
        ['status' => true, 'message' => 'Akun berhasil ditambahkan.']
    );


