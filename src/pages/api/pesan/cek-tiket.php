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
    'message' => 'Rute tidak tersedia',
    'query' => ['asal' => $_GET['asal'], 'tujuan' => $_GET['tujuan']]
]);

$tiketService = $app->getService('TiketService');
$tiket = $tiketService->cekTiketTersedia($rute['rute'], $_GET['kategori']);

if (false === $tiket) response()->badRequest([
    'message' => 'Kategori dengan rute tersebut tidak tersedia, mohon coba lagi atau hubungi pihak terkait.',
    'query' => ['asal' => $_GET['asal'], 'tujuan' => $_GET['tujuan'], 'kategori' => $_GET['kategori']]
]);

//$listPesanan = $pemesananService->listKursiDipesanBerdasarkanTanggalDanRute($tanggalKeberangkatan, $rute['rute']);

response()->toJson([
    'tanggal_keberangkatan' => $tanggalKeberangkatan->format('Y-m-d'),
    'rute' => ['reversed' => $rute['reversed'], 'rute' => $rute['rute']->toArray()],
    'list_mobil_tersedia' => [
        [
            'mobil' => [ 'id' => 1, 'merk' => 'Toyota Avanza X', 'jumlah_kursi' => 7, 'plat_nomor' => 'BM 2819 QQ' ],
            'driver' => [ 'id' => 2, 'nama' => 'Alex Luis', 'kontak' => '082869136322' ],
            'total_kursi_penumpang' => 7,
            'kursi_terpesan' => [1, 2, 4],
            'kursi_tersedia' => [3, 5, 6, 7],
            'list_kursi' => [
                ['nomor' => 1, 'tersedia' => false],
                ['nomor' => 2, 'tersedia' => false],
                ['nomor' => 3, 'tersedia' => true],
                ['nomor' => 4, 'tersedia' => false],
                ['nomor' => 5, 'tersedia' => true],
                ['nomor' => 6, 'tersedia' => true],
                ['nomor' => 7, 'tersedia' => true]
            ]
        ],[
            'mobil' => [ 'id' => 2, 'merk' => 'Toyota Avanza Y', 'jumlah_kursi' => 7, 'plat_nomor' => 'BM 2819 QQ' ],
            'driver' => [ 'id' => 3, 'nama' => 'Alex Luis', 'kontak' => '082869136322' ],
            'total_kursi_penumpang' => 7,
            'kursi_terpesan' => [6, 7],
            'kursi_tersedia' => [1, 2, 3, 4, 5],
            'list_kursi' => [
                ['nomor' => 1, 'tersedia' => true],
                ['nomor' => 2, 'tersedia' => true],
                ['nomor' => 3, 'tersedia' => true],
                ['nomor' => 4, 'tersedia' => true],
                ['nomor' => 5, 'tersedia' => true],
                ['nomor' => 6, 'tersedia' => false],
                ['nomor' => 7, 'tersedia' => false]
            ]
        ]
    ],
    'list_jam_keberangkatan_tersedia' => [
        [ 'id' => 1, 'jam' => '13:00', 'alias' => 'SIANG' ],
        [ 'id' => 2, 'jam' => '20:00', 'alias' => 'MALAM' ]
    ]
]);

