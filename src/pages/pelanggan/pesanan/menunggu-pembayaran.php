<?php

if (false === session()->isAuthenticatedAs('pelanggan')) html_unauthorized();
/** @var $pesanan Pesanan  */
$listPesanan = app()->getManager()->getService('PemesananService')->listPesananMenungguPembayaran(session()->auth());


?>
<div>
    <div class="w-full flex justify-between border-b pb-1 mb-4">
        <h2 class="antialiased tracking-normal font-sans text-2xl font-semibold leading-relaxed text-gray-900">Menunggu Pembayaran</h2>
    </div>
    <div class="mb-4">
        <div class="mb-8">
            <?php if(count($listPesanan) > 0): ?>
                <div class="grid sm:grid-cols-3 gap-4">
                    <?php foreach($listPesanan as $pesanan): ?>
                        <div>
                            <div class="border rounded shadow-md bg-white">
                                <div class="font-medium font-sans border-b p-4 flex justify-between items-center">
                                    <div><?= strtoupper($pesanan->getRute()) ?></div>
                                    <div><?= tanggal($pesanan->getTanggalKeberangkatan()) ?> </div>
                                </div>
                                <div class="p-4">
                                    <p class="font-semibold">
                                        <?= $pesanan->getNamaPemesan() ?> (<?= $pesanan->getKontakPemesan() ?>)
                                    </p>
                                    <p class="font-light mb-2">
                                        Total Tagihan: Rp <?= rupiah($pesanan->getTotalTarif()); ?> (@<?= rupiah($pesanan->getTotalTarif() / count($pesanan->getListKursi())) ?>/kursi)
                                        <br> Dibayar: Rp <?= rupiah(($pesanan->getTotalDibayarkan())) ?>
                                        <br>Jumlah Kursi: <?= count($pesanan->getListKursi()); ?> kursi
                                    <?php if($pesanan->getTotalDibayarkan() >= ( $pesanan->getTotalTarif() / count($pesanan->getListKursi()))):   ?>
                                        <div class="bg-green-600 text-sm px-2 py-1 font-light rounded text-white inline">Sudah DP 50 %</div>
                                    <?php else: ?>
                                    <div class="bg-red-600 text-sm px-2 py-1 font-light rounded text-white inline">Belum DP 50 %</div>
                                    <?php endif;?>
                                </div>
                                <div class="font-medium font-sans border-t p-4 text-sm flex justify-between items-center">
                                    <p>Sisa: Rp. <?= rupiah($pesanan->getTotalTarif() - $pesanan->getTotalDibayarkan()) ?></p>
                                    <a href="<?= site_url('pelanggan/pesanan/detail?nomor=') . $pesanan->getNomorPesanan() ?>" class="text-sm text-red-400 hover:underline">Bayar</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="w-full text-center">Tidak ada jadwal perjalanan.</div>
            <?php endif; ?>
        </div>
    </div>
</div>