<?php

if (
    request()->notGetRequest() ||
    false === request()->has(['tanggal_keberangkatan', 'asal', 'tujuan', 'kategori'])
) response()->notFound();

$app = app()->getManager();

$tanggalKeberangkatan = date_create($_GET['tanggal_keberangkatan']);
$pemesananService = $app->getService('PemesananService');
$greatherOrSameThanToday = $pemesananService->validasiTanggalKeberangkatan($tanggalKeberangkatan);

if (false === $greatherOrSameThanToday) response()->badRequest([
    'message' => 'Pilihan tanggal tidak sesuai, mohon periksa kembali',
    'query' => ['tanggal_keberangkatan' => $_GET['tanggal_keberangkatan']]
]);

$ruteService = $app->getService('RuteService');
$rute = $ruteService->cekRuteTersedia($_GET['asal'], $_GET['tujuan']);

if (false == $rute) response()->badRequest([
    'message' => 'Saat ini rute tersebut tidak tersedia, mohon coba yang lain atau hubungi kami di nomor terkait.',
    'query' => ['asal' => $_GET['asal'], 'tujuan' => $_GET['tujuan']]
]);

$tiketService = $app->getService('TiketService');
$tiket = $tiketService->cekTiketTersedia($rute['rute'], $_GET['kategori']);

if (false === $tiket) response()->badRequest([
    'message' => 'Kategori dengan rute tersebut tidak tersedia, mohon coba lagi atau hubungi pihak terkait.',
    'query' => ['asal' => $_GET['asal'], 'tujuan' => $_GET['tujuan'], 'kategori' => $_GET['kategori']]
]);

$listJamKeberangkatan = $app->getService('JamKeberangkatanService')->listJamKeberangkatan();

$listMobilDanKursi = [];
if ($tanggalKeberangkatan->format('Y-m-d') == date('Y-m-d')) {
    $listMobilDanKursi = $pemesananService->listMobilDanKursiByDate($_GET['asal']);
}

response()->jsonOk([
    'tanggal_keberangkatan' => $tanggalKeberangkatan->format('Y-m-d'),
    'rute' => ['reversed' => $rute['reversed'], 'rute' => $rute['rute']->toArray()],
    'list_mobil_tersedia' => $listMobilDanKursi,
    'list_jam_keberangkatan_tersedia' => array_map(fn($item) => $item->toArray(), $listJamKeberangkatan)
]);

