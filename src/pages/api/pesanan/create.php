<?php

if (
    request()->notPostRequest() ||
    false === request()->has(['tanggal_keberangkatan', 'asal', 'tujuan', 'kategori', 'list_nomor_kursi', 'mobil'])
) response()->notFound();

$app = app()->getManager();

$tanggalKeberangkatan = date_create($_POST['tanggal_keberangkatan']);
$pemesananService = $app->getService('PemesananService');
$greatherOrSameThanToday = $pemesananService->validasiTanggalKeberangkatan($tanggalKeberangkatan);

if (false === $greatherOrSameThanToday) response()->badRequest([
    'message' => 'Pilihan tanggal tidak sesuai, mohon periksa kembali',
    'query' => ['tanggal_keberangkatan' => $_POST['tanggal_keberangkatan']]
]);

$ruteService = $app->getService('RuteService');
$rute = $ruteService->cekRuteTersedia($_POST['asal'], $_POST['tujuan']);

if (false == $rute) response()->badRequest([
    'message' => 'Rute tidak tersedia',
    'query' => ['asal' => $_POST['asal'], 'tujuan' => $_POST['tujuan']]
]);

$tiketService = $app->getService('TiketService');
$tiket = $tiketService->cekTiketTersedia($rute['rute'], $_POST['kategori']);

if (false === $tiket) response()->badRequest([
    'message' => 'Kategori dengan rute tersebut tidak tersedia, mohon coba lagi atau hubungi pihak terkait.',
    'query' => ['asal' => $_POST['asal'], 'tujuan' => $_POST['tujuan'], 'kategori' => $_POST['kategori']]
]);

$ListKursi = is_array($_POST['list_nomor_kursi']) ? $_POST['list_nomor_kursi'] : explode(',', $_POST['list_nomor_kursi']);

//$isKursiTersediaValid = $pemesananService->validasiKursiDipesan($tanggalKeberangkatan, $tiket, $_POST['mobil'], $_POST['list_nomor_kursi']);
//if (false == $isKursiTersediaValid) response()->badRequest([
//    'message' => 'Nomor kursi yang dipilih sudah dipesan terlebih dahulu, mohon pilih opsi lain.',
//    'query' => ['list_nomor_kursi' => $_POST['list_nomor_kursi']]
//]);

$pesanan = $pemesananService->buatPesananBaru(
    $tanggalKeberangkatan,
    $_POST['jam_keberangkatan'],
    $tiket,
    $_POST['mobil'],
    $ListKursi,
    $rute['reversed']
);

session()->add('nomor_pemesanan', $pesanan->getNomorPesanan());

response()->jsonOk(['nomor_pemesanan' => $pesanan->getNomorPesanan()], 201);

