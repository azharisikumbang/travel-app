<?php

if (false === session()->isAuthenticatedAs('admin')) html_unauthorized();
load_externals(function ($externalDir) {
    require_once $externalDir . 'dompdf/autoload.inc.php';
});

use Dompdf\Dompdf;

$year = $_GET['tahun'] ?? date('Y');
$month = $_GET['bulan'] ?? date('m');
$date = $_GET['tanggal'] ?? 0;

$listData = app()->getManager()->getService('PemesananService')->laporanPenjualanBerdasarkanKategoriPenumpang(
    $year,
    $month,
    $date
);


ob_clean();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laporan Penjualan - PT. Sorek Wisata Transport</title>
    <style type="text/css">
        body {
            font-size: 11px;
            line-height: 14px;
        }
        table td {
            padding: 4px 8px;
        }
        table thead tr th {
            padding: 4px 8px;
            background: #f7f7f7;
        }
    </style>
</head>
<body>
    <div style="text-align: center">
        <h1>Laporan Penjualan PT Sorek Wisata Transport</h1>
        <p>Dibuat : <?= date('d/m/Y H:i:s') ?> WIB - oleh <?= session()->auth()->getUsername() ?></p>
    </div>
    <table border="1" style="border-collapse: collapse; text-align: center; width: 100%">
        <thead>
        <tr>
            <th>No</th>
            <th>Tanggal Keberangkatan</th>
            <th>Kategori Pemesan</th>
            <th>Uang Masuk (Rp)</th>
            <th>Uang Keluar (Rp)</th>
            <th>Keterangan</th>
        </tr>
        </thead>
        <tbody>
         <?php
            $totalUangMasuk = 0;
            $totalUangKeluar = 0;
            $no = 1;
            foreach ($listData as $data):
                $totalUangMasuk += $data['uang_masuk'];
                $totalUangKeluar += $data['uang_keluar'];
            ?>
            <tr>
                <td><?= $no++; ?></td>
                <td><?= $data['periode'] ?></td>
                <td><?= $data['kategori'] ?></td>
                <td>Rp <?= rupiah($data['uang_masuk']) ?></td>
                <td>Rp <?= rupiah($data['uang_keluar']) ?></td>
                <td><?= $data['keterangan'] ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr style="font-weight: 600">
                <td colspan="3" style="text-align: right">Total</td>
                <td>Rp <?= rupiah($totalUangMasuk) ?></td>
                <td>Rp <?= rupiah($totalUangKeluar) ?></td>
                <td></td>
            </tr>
        </tfoot>
    </table>
    <div style="margin-top: 40px">
        <div style="text-align: center; width: 200px; float:right">
            <div>Sorek, <?= tanggal(date_create())  ?></div>
            <div style="margin-top: 60px;">
                <span style="font-weight: bold; text-decoration: underline; display: block">Febri Selvy Andri</span>
                <span>Pimpinan</span>
            </div>
        </div>
        <div style="clear:both"></div>
    </div>
</body>
</html>
<?php

$content = ob_get_clean();
$dompdf = new Dompdf();
$dompdf->loadHtml($content);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();
$dompdf->stream('Laporan-Penjualan-PT-Sorek_WISATA_TRANSPORT.pdf', ['Attachment' => false]);
//$output = $dompdf->output();

?>

