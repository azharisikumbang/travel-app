<?php

/** @var $pesanan Pesanan  */
$app = app()->getManager();
$driver = $app->getService('DriverService')->findByAkun(session()->auth());
$listRuteDriver = $app->getService('DriverService')->listRuteSaya($driver);
$listJadwalPerjalananPelanggan = $app->getService('PemesananService')->listPemesananHarianBerdasarkanDriver($driver, $listRuteDriver);

?>
<div>
    <div class="w-full flex justify-between border-b pb-1 mb-4">
        <h2 class="antialiased tracking-normal font-sans text-2xl font-semibold leading-relaxed text-gray-900">Daftar Penumpang</h2>
    </div>
    <div class="mb-4">
        <div class="mb-8">
            <h3 class="font-sans text-lg text-gray-800 font-medium mb-4">Tanggal Keberangkatan: <?= tanggal(date_create('now')) ?> (Hari ini)</h3>
            <?php if(count($listJadwalPerjalananPelanggan) > 0): ?>
                <div class="grid grid-cols-3 gap-4">
                    <?php foreach($listJadwalPerjalananPelanggan as $pesanan): ?>
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
                                        Jemput: <?= $pesanan->getTitikJemput() ?>
                                    </p>
                                    <p class="font-light italic">
                                        Nomor Kursi: <?= implode(", ", array_map(fn($item) => $item->getNomorKursi(), $pesanan->getListKursi())); ?>
                                    </p>
                                </div>
                                <div class="font-medium font-sans border-t p-4 text-sm flex justify-between items-center">
                                    <div class="bg-<?= $pesanan->getStatusBuktiPembayaran()->getColor() ?>-600 text-sm px-2 py-1 font-light rounded text-white">PEMBAYARAN <?= $pesanan->getStatusBuktiPembayaran()->getDisplayName() ?></div>
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