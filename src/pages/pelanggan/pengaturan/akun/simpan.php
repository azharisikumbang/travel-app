<?php

if (
    false === session()->isAuthenticatedAs('pelanggan') ||
    request()->notPostRequest() ||
    false === request()->has(['nama', 'kontak', 'kategori'])
) response()->notFound();


$manager = app()->getManager();
$umum = $manager->getService('KategoriPelangganService')->cari('umum');
$photoIdentitas = null;

if($umum->getId() != $_POST['kategori']) {
    if(!isset($_FILES['photo_identitas'])) {
        response()->redirectTo(site_url('pelanggan/pengaturan/akun'), ['status' => false, 'message' => 'Kategori mahasiswa atau imappel harus menyediakan kartu identitas mahasiswa, mohon coba lagi.']);
    }

    $photoIdentitas = $_FILES['photo_identitas'];

    if($photoIdentitas['error']) {
        response()->redirectTo(site_url('pelanggan/pengaturan/akun'), ['status' => false, 'message' => 'Gagal mengupload foto identitas. Mohon coba kembali.']);
    }
}

$updated = app()->getManager()->getService('PelangganService')->updateInformasiSaya(
    $_POST['nama'],
    $_POST['kontak'],
    $_POST['kategori'],
    $photoIdentitas
);

if (!$updated)
    response()->redirectTo(site_url('pelanggan/pengaturan/akun'), ['status' => false, 'message' => 'Gagal mengubah pengaturan akun, mohon coba kembali']);

response()
    ->redirectTo(
        site_url('pelanggan/pengaturan/akun'),
        ['status' => true, 'message' => 'Akun berhasil diperbaharui.']
    );