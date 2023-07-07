<?php

// cek tarif apakah sesuai
$tanggalKeberangkatan = date_create($_POST['tanggal_keberangkatan']);

$tiketService = app()->getManager()->getService('TiketService');
if (false === $tiketService->cekTanggalKeberangkatanValid($tanggalKeberangkatan)) {
    response()->badRequest("Tanggal yang dipesan sudah terlewat, mohon periksa kembali.");
}

$asal = $_POST['asal_keberangkatan'];
$tujuan = $_POST['tujuan_keberangkatan'];

if(true === $tiketService->cekApakahAsalDanTujuanSama($asal, $tujuan)) {
    response()->badRequest("Asal dan tujuan keberangkatan berada di kota yang sama, mohon periksa kembali.");
}

$kategori = $_POST['kategori_penumpang'];
$tarifService = app()->getManager()->getService('TarifService');
$tarifEntity = $tarifService->cariTarif($asal, $tujuan, $kategori);
if (null === $tarifEntity) {
    response()->badRequest("RuteHarian dan kategori dipilih tidak tersedia, mohon periksa kembali.");
}

// validasi ulang kursi apakah tersedia
$tiketService = app()->getManager()->getService('TiketService');
$kursiDipesan = $_POST['kursi_dipesan'];

$jamKeberangkatan = $_POST['jam_keberangkatan'];
if(false === $tiketService->validasiKursiTersedia($kursiDipesan, $tanggalKeberangkatan, $jamKeberangkatan, $tarifEntity)) {
    response()->badRequest("Nomor kursi yang anda pilih telah dipesan, mohon pilih yang lain.");
}

// buat kode pesanan {MERK/TAHUN/BULAN/NOMOR}
$pemesananService = app()->getManager()->getService('PemesananService');
$tanggalPemesanan = date_create();
$nomorPesanan = $pemesananService->buatNomorPemesanan($tanggalPemesanan);
$pesanan = $pemesananService->buatPesananBaru(
    $nomorPesanan,
    $tanggalKeberangkatan,
    $jamKeberangkatan,
    $tarifEntity,
    $kursiDipesan
);

// refresh
$pesanan = $pemesananService->cariPesananBerdasarkanNomorPesanan($pesanan->getNomorPesanan(), true);

session()->add('pesanan', $pesanan->toArray());

response()->jsonOk(
    session()->all()
);
// simpan pesanan

// simpan kursi