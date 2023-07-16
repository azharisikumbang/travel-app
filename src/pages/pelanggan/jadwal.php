<?php

if (false === session()->isAuthenticatedAs('pelanggan')) html_unauthorized();
/** @var $pesanan Pesanan  */
$listJadwalPerjalananPelanggan = app()->getManager()->getService('PemesananService')->listJadwalPerjalananPelanggan(session()->auth());


?>
<div>
    <div class="w-full flex justify-between border-b pb-1 mb-4">
       <h2 class="antialiased tracking-normal font-sans text-2xl font-semibold leading-relaxed text-gray-900">Perjalanan Saya</h2>
    </div>
    <div class="mb-4 border-sky-400 text-sky-600 border-2 py-2 px-4 rounded-lg">
        <p class="font-sans">
            <span class="font-semibold">Penting!</span>
            Pesanan yang ditampilkan hanya pesanan yang status
            <span class="font-medium">pembayaran telah dilakukan minimal 50% dari total tagihan</span>, sisa pembayaran bisa dilakukan di loket keberangkatan.
            Untuk informasi pesanan tiket lainnya silahkan cek di menu <a href="" class="underline hover:text-blue-400">semua pesanan</a>.
        </p>
    </div>
    <div class="mb-4">
        <div class="mb-8">
            <h3 class="font-sans text-lg text-gray-800 font-medium mb-4">Tanggal Keberangkatan: <?= tanggal(date_create('now')) ?> (Hari ini)</h3>
            <?php if(count($listJadwalPerjalananPelanggan['hari_ini']) > 0): ?>
            <div class="grid sm:grid-cols-3 gap-4">
            <?php foreach($listJadwalPerjalananPelanggan['hari_ini'] as $pesanan): ?>
                <div>
                    <div class="border rounded shadow-md bg-white">
                        <div class="font-medium font-sans border-b p-4 flex justify-between items-center">
                            <div><?= strtoupper($pesanan->getRute()) ?></div>
                            <div><?= $pesanan->getJamKeberangkatan() ?> WIB</div>
                        </div>
                        <div class="p-4">
                            <p class="font-semibold">
                                <?= $pesanan->getNamaPemesan() ?> (<?= $pesanan->getKontakPemesan() ?>)
                            </p>
                            <p class="font-light mb-2">
                                Jemput: <?= $pesanan->getTitikJemput() ?> <br>
                                Nomor Kursi: <?= implode(", ", array_map(fn($item) => $item->getNomorKursi(), $pesanan->getListKursi())); ?>
                            </p>
                            <div class="flex gap-2">
                                <?php if($pesanan->getTotalDibayarkan() >= ( $pesanan->getTotalTarif() / count($pesanan->getListKursi()))):   ?>
                                <div class="bg-green-600 text-sm px-2 py-1 font-light rounded text-white inline">Sudah DP 50 %</div>
                                <?php endif; ?>
                                <div class="bg-<?= $pesanan->getStatusBuktiPembayaran()->getColor() ?>-600 text-sm px-2 py-1 font-light rounded text-white">PEMBAYARAN <?= $pesanan->getStatusBuktiPembayaran()->getDisplayName() ?></div>
                            </div>
                        </div>
                        <div class="font-medium font-sans border-t p-4 text-sm flex justify-between items-center">
                            <?php if($pesanan->getFileTiket()): ?>
                            <a href="<?= site_url('api/pelanggan/pesanan/unduh-tiket?nomor=' . $pesanan->getNomorPesanan()) ?>" class="text-red-500 text-sm block hover:text-red-600 cursor-pointer">Download Tiket</a>
                            <?php else: ?>
                            <div></div>
                            <?php endif ?>
                            <a href="<?= site_url('pelanggan/pesanan/detail?nomor=') . $pesanan->getNomorPesanan() ?>" class="text-sm text-red-400 hover:underline">Detail</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="w-full text-center">Tidak ada jadwal perjalanan.</div>
            <?php endif; ?>
        </div>
        <div class="mb-8">
            <h3 class="font-sans text-lg text-gray-800 font-medium mb-4">Jadwal Keberangkatan: Akan Datang</h3>
            <?php if(count($listJadwalPerjalananPelanggan['lainnya']) > 0): ?>
                <div class="grid sm:grid-cols-3 gap-4">
                    <?php foreach($listJadwalPerjalananPelanggan['lainnya'] as $pesanan): ?>
                        <div>
                            <div class="border rounded shadow-md bg-white">
                                <div class="font-medium font-sans border-b p-4 flex justify-between items-center">
                                    <div><?= tanggal($pesanan->getTanggalKeberangkatan()) ?></div>
                                    <div><?= $pesanan->getJamKeberangkatan() ?> WIB</div>
                                </div>
                                <div class="p-4">
                                    <p class="font-semibold">
                                        <?= $pesanan->getNamaPemesan() ?> (<?= $pesanan->getKontakPemesan() ?>)
                                    </p>
                                    <p class="font-light mb-2">
                                        Jemput: <?= $pesanan->getTitikJemput() ?>
                                    </p>
                                    <p class="font-light italic">
                                        Rute: <?= $pesanan->getRute() ?>
                                    </p>
                                    <p class="font-light italic">
                                        Nomor Kursi: <?= implode(", ", array_map(fn($item) => $item->getNomorKursi(), $pesanan->getListKursi())); ?>
                                    </p>
                                </div>
                                <div class="font-medium font-sans border-t p-4 text-sm flex justify-between items-center">
                                    <div class="bg-gray-600 text-sm px-2 py-1 font-light rounded text-white">Tarif: Rp <?= rupiah($pesanan->getTotalTarif()) ?></div>
                                    <a href="<?= site_url('pelanggan/pesanan/detail?nomor=') . $pesanan->getNomorPesanan() ?>" class="text-sm text-red-400 hover:underline">Detail</a>
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