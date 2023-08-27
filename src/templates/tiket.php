<?php if (!isset($pesanan)) html_not_found(); /** @var $pesanan Pesanan */ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tiket <?= $pesanan->getNomorPesanan() ?> - PT. Sorek Wisata Transport</title>
    <style type="text/css">
        body {
            font-size: 11px;
            line-height: 14px;
        }
        ol li {
            line-height: 16px;
        }
        table td {
            padding: 0px 8px;
        }
    </style>
</head>
<body>
<div>
    <ul style="margin: 0; padding: 0; display: inline-table; width: 100%; margin-bottom: 4px">
        <li style="display:inline-table; width: 50%; background: black; text-align:center; color: white; border: 1px solid #000;">
            <div style="padding: 10px 0px">PT. SOREK WISATA TRANSPORT</div>
        </li>
        <li style="display:inline-table; width: 49.6%; background: #e1e11d; text-align:right; color: black; border: 1px solid #000;">
            <div style="padding: 10px 20px">
                Telp. 0812 6828 0330
            </div>
        </li>
    </ul>
    <div>
        <div style="float:left">
            <table border="1" style="border-collapse: collapse; margin-bottom: 8px; width:520px" cellpadding="4px">
                <tr>
                    <td style="width: 120px;">
                        <span style="display: block; text-decoration: underline; font-weight: 700;">Nomor Tiket</span>
                        <span style="font-style: italic;">Ticket Number</span>
                    </td>
                    <td colspan="2">
                        <p><?= $pesanan->getNomorPesanan() ?></p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span style="display: block; text-decoration: underline; font-weight: 700;">    Nama Penumpang
                        </span>
                        <span style="font-style: italic">Name of Pessenger</span>
                    </td>
                    <td colspan="2">
                        <p><?= $pesanan->getNamaPemesan() ?> (<?= $pesanan->getKategoriPelanggan() ?>)</p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span style="display: block; text-decoration: underline; font-weight: 700;">
                            No. HP / Alamat
                        </span>
                        <span style="font-style: italic">HP Number / Address</span>
                    </td>
                    <td colspan="2">
                        <p><?= $pesanan->getKontakPemesan() ?> / <?= $pesanan->getTitikJemput() ?></p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span style="display: block; text-decoration: underline; font-weight: 700;">Tanggal Berangkat
                        </span>
                        <span style="font-style: italic">Depature Date</span>
                    </td>
                    <td colspan="2">
                        <p><?= tanggal($pesanan->getTanggalKeberangkatan()) ?></p>
                    </td>
                </tr>
                <tr>
                    <td>
                    <span style="display: block; text-decoration: underline; font-weight: 700;">
                        Jam
                    </span>
                        <span style="font-style: italic">Time</span>
                    </td>
                    <td colspan="2">
                        <p><?= $pesanan->getJamKeberangkatan() ?></p>
                    </td>
                </tr>
                <tr>
                    <td>
                    <span style="display: block; text-decoration: underline; font-weight: 700;">
                        Tujuan
                    </span>
                        <span style="font-style: italic">Destination</span>
                    </td>
                    <td colspan="2">
                        <p><?= $pesanan->getKotaAsal() ?> - <?= $pesanan->getKotaTujuan() ?></p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span style="display: block; text-decoration: underline; font-weight: 700;">
                            Uang Muka
                        </span>
                        <span style="font-style: italic">Advanced Money</span>
                    </td>
                    <td>
                        <p>Rp <?= rupiah($pesanan->getTotalDibayarkan()) ?></p>
                    </td>
                    <td>
                        <p>Sisa: Rp <?= rupiah($pesanan->getTotalTarif() - $pesanan->getTotalDibayarkan()) ?></p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span style="display: block; text-decoration: underline; font-weight: 700;">
                            Tarif
                        </span>
                        <span style="font-style: italic">Fare</span>
                    </td>
                    <td colspan="2">
                        <p>Rp <?= $pesanan->getTotalTarif() ?></p>
                    </td>
                </tr>
            </table>
            <table border="1" style="border-collapse: collapse; margin-bottom: 8px; width:520px" cellpadding="4px">
                <tr>
                    <td style="width: 120px;">
                        <span style="display: block; text-decoration: underline; font-weight: 700;">No. Tempat Duduk</span>
                        <span style="font-style: italic;">Seat Number</span>
                    </td>
                    <td>
                        <?php
                            $listNomorKursi = array_map(fn ($item) => $item->getNomorKursi(), $pesanan->getListKursi());
                            echo implode(", ", $listNomorKursi)
                        ?>
                        (total: <?= count($listNomorKursi) ?> kursi)
                    </td>
                </tr>
            </table>
            <div style="width:520px">
                <table border="1" style="border-collapse: collapse; margin-bottom: 8px; width: 380px; float:left" cellpadding="4px">
                    <tr>
                        <td style="width: 120px;">
                            <span style="display: block; text-decoration: underline; font-weight: 700;">
                                Pengemudi
                            </span>
                            <span style="font-style: italic;">Driver</span>
                        </td>
                        <td>
                            <p><?= $pesanan->getDriver() ?></p>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 120px;">
                            <span style="display: block; text-decoration: underline; font-weight: 700;">
                                No. Polisi
                            </span>
                            <span style="font-style: italic;">Vehicle Registration</span>
                        </td>
                        <td>
                            <p><?= $pesanan->getMobil() ?></p>
                        </td>
                    </tr>
                </table>
                <table style="float:right">
                    <tr>
                        <td style="text-align: center; padding-top: 10px">
                            .......................................
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: center; padding-top: 30px">Pengurus</td>
                    </tr>
                </table>
                <div style="clear:both"></div>
            </div>
        </div>
        <div style="width:180px; float:right">
        <div style="
            border: 1px solid darkgray;
            width: 430px;
            transform: rotate(-90deg) translate(-125px, -120px);
            ">
            <ol>
                <li>Jemput antar ke alamat dalam batas tertentu. </li>
                <li>Bagasi cuma-cuma 10 Kg per orang, kelebihan dikenakan biaya.</li>
                <li>Pembatalan tiket dilaporkan paling lambar 2 jam sebelum pemberangkatan.</li>
                <li>Pemesan mentransfer uang muka (DP) 50% dari total biaya tarif perjalanan dan atau bisa langsung membayar lunas dari total biaya tarif perjalanan</li>
                <li>Pembatalan tiket pada jam sebelum pemberangkatan dikenakan biaya 50 %.</li>
                <li>Bila terjadi kecekalakaan, kerugian dalam bentuk harta benda tidak ditanggung oleh perusahaan</li>
                <li>Dilarang membawa barang terlarang / mewah atau barang yang baunya menggangu penumpang.</li>
            </ol>
        </div>
        <div style="clear:both"></div>
    </div>
    </div>
</div>
</body>
</html>
