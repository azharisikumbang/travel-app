<?php

if (false === session()->isAuthenticatedAs('pelanggan')) html_unauthorized();

/** @var $pesanan Pesanan  */
$me = session()->auth();
$pesanan = app()->getManager()->getService('PemesananService')->cariPesananBerdasarkanNomorPesananDanPemesan($_GET['nomor'], $me);

if ($me->getId() != $pesanan->getPemesanId()) html_not_found();

?>
<main x-data="container">
    <nav class="block w-full max-w-full bg-transparent text-white shadow-none transition-all px-0 py-1 border-b-2">
        <div class="flex flex-col-reverse justify-between gap-6 md:flex-row md:items-center">
            <h2 class="block antialiased tracking-normal font-sans text-2xl font-semibold leading-relaxed text-gray-900">Tiket No. <?= $pesanan->getNomorPesanan() ?></h2>
            <div class="flex items-center gap-4">
                <span class="font-sans text-gray-500">Sekarang: <?php echo tanggal(date_create()) ?></span>
                <a @click="window.location.reload()" class="underline text-gray-500 hover:text-gray-600 cursor-pointer">Muat Ulang</a>
            </div>
        </div>
    </nav>
    <div id="content" class="mt-8 w-full overflow-hidden grid sm:grid-cols-2 gap-4">
        <div>
            <div class="rounded-lg border bg-white p-4 sm:p-8">
                <h6 class="rounded-tl rounded-tr font-sans text-xl font-semibold text-gray-700 mb-2">Informasi Pemesanan (Tiket)</h6>
                <div class="w-full border-b py-2 mb-2">
                    <label class="text-gray-700 font-medium">Nomor Tiket</label>
                    <p class="w-full"><?= $pesanan->getNomorPesanan() ?></p>
                </div>
                <div class="w-full border-b py-2 mb-2">
                    <label class="text-gray-700 font-medium">Tanggal Tiket Dipesan</label>
                    <p class="w-full"><?= tanggal($pesanan->getTanggalPemesanan(), false, true) ?> WIB</p>
                </div>
                <div class="w-full border-b py-2 mb-2">
                    <label class="text-gray-700 font-medium">Tanggal Keberangkatan</label>
                    <p class="w-full"><?= tanggal($pesanan->getTanggalKeberangkatan()) ?></p>
                </div>
                <div class="w-full border-b py-2 mb-2">
                    <label class="text-gray-700 font-medium">Rute</label>
                    <p class="w-full"><?= $pesanan->getRute() ?></p>
                </div>
                <div class="w-full border-b py-2 mb-2">
                    <label class="text-gray-700 font-medium">Mobil dan Driver</label>
                    <p class="w-full"><?= $pesanan->getMobil() ?> - <?= $pesanan->getDriver() ?></p>
                </div>
                <div class="w-full border-b py-2 mb-2">
                    <label class="text-gray-700 font-medium">Nomor Kursi</label>
                    <p class="w-full"><?= $pesanan->getListKursiAsString() ?> (total: <?= count($pesanan->getListKursi()) ?> kursi)</p>
                </div>
                <div class="w-full border-b py-2 mb-2">
                    <label class="text-gray-700 font-medium">Tagihan</label>
                    <p class="w-full">
                        Rp <?= rupiah($pesanan->getTotalTarif()) ?> (<span>@Rp <?= rupiah($pesanan->getTotalTarif() / count($pesanan->getListKursi())) ?>/kursi</span>)
                    </p>
                </div>
                <div class="w-full border-b py-2 mb-2">
                    <label class="text-gray-700 font-medium">Tiket</label>
                    <?php if($pesanan->getFileTiket()): ?>
                    <a href="<?= site_url('api/pelanggan/pesanan/unduh-tiket?nomor=' . $pesanan->getNomorPesanan()) ?>" class="text-red-500 underline text-sm block hover:text-red-600 cursor-pointer">Unduh untuk melihat.</a>
                    <?php else: ?>
                    <span class="block">-</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div>
            <div class="rounded-lg border bg-white p-4 sm:p-8 mb-4">
                <h6 class="rounded-tl rounded-tr font-sans text-xl font-semibold text-gray-700 mb-2">Informasi Pemesan</h6>
                <div class="w-full border-b py-2 mb-2">
                    <label class="text-gray-700 font-medium">Atas Nama Pemesan</label>
                    <p class="w-full"><?= $pesanan->getNamaPemesan() ?></p>
                </div>
                <div class="w-full border-b py-2 mb-2">
                    <label class="text-gray-700 font-medium">Kontak Pemesan</label>
                    <p class="w-full"><?= $pesanan->getKontakPemesan() ?></p>
                </div>
                <div class="w-full border-b py-2 mb-2">
                    <label class="text-gray-700 font-medium">Alamat Jemput</label>
                    <p class="w-full"><?= $pesanan->getTitikJemput() ?></p>
                </div>
            </div>
            <div class="rounded-lg border bg-white p-8 mb-4">
                <h6 class="rounded-tl rounded-tr font-sans text-xl font-semibold text-gray-700 mb-2">Informasi Pembayaran</h6>
                <div class="w-full border-b py-2 mb-2">
                    <label class="text-gray-700 font-medium">Atas Nama Pembayaran</label>
                    <p class="w-full"><?= $pesanan->getNamaPembayaran() ?? '-' ?></p>
                </div>
                <div class="w-full border-b py-2 mb-2">
                    <label class="text-gray-700 font-medium">Bank Pembayaran</label>
                    <p class="w-full"><?= $pesanan->getBankPembayaran() ?? '-' ?></p>
                </div>
                <div class="w-full border-b py-2 mb-2">
                    <label class="text-gray-700 font-medium">Nominal Dibayarkan</label>
                    <p class="w-full">
                        Rp <?= rupiah($pesanan->getTotalDibayarkan()) ?? '-'; ?>
                        <span>(<?= $pesanan->getTotalTarif() <= $pesanan->getTotalDibayarkan() ? 'Lunas' : 'Sisa: Rp' . rupiah($pesanan->getTotalTarif() - $pesanan->getTotalDibayarkan()) ?>)</span>
                        </span>
                    </p>
                </div>
                <div class="w-full border-b py-2 mb-2">
                    <label class="text-gray-700 font-medium">Bukti Pembayaran</label>
                    <?php if($pesanan->getBuktiPembayaran()): ?>
                        <a href="<?= site_url('api/pelanggan/pesanan/unduh-bukti-pembayaran?nomor=' . $pesanan->getNomorPesanan()) ?>" class="text-red-500 underline text-sm block hover:text-red-600 cursor-pointer">Unduh untuk melihat.</a>
                    <?php else: ?>
                        <span class="block">-</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>
