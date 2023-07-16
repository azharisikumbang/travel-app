<?php

if (false === session()->isAuthenticatedAs('pelanggan')) html_unauthorized();

$manager = app()->getManager();
$user = session()->auth();
$pelangganService = $manager->getService('PelangganService');
$pemesananService = $manager->getService('PemesananService');
$me = $pelangganService->informasiSaya($user);
$listPemesanan = $pelangganService->daftarPemesananSaya($pemesananService, $me);

?>
<main>
    <div class="w-full border-b pb-1 mb-4">
        <h2 class="antialiased tracking-normal font-sans text-2xl font-semibold leading-relaxed text-gray-900">Dashboard</h2>
    </div>
    <div class="w-full flex flex-col sm:flex-row gap-4">
        <div class="w-full sm:w-8/12">
            <div class="border-gray-200 border p-4 sm:my-4">
                Selamat datang, <?= $me->getNama() ?>.
            </div>
        </div>
        <div class="w-full sm:w-4/12">
            <div class="border-gray-200 border p-4 my-4 ">
                <h3 class="font-sans text-md font-medium mb-4">Informasi</h3>
                <ul>
                    <?php if(count($listPemesanan['hari_ini']) >= 1): ?>
                    <li class="border-b border-gray-200 py-2">
                        <a href="<?= site_url('pelanggan/jadwal') ?>" class="hover:text-gray-600 hover:underline">Anda memliki perjalanan hari ini, klik untuk informasi lebih lanjut.</a>
                    </li>
                    <?php endif; ?>
                    <?php if(count($listPemesanan['unconfirmed']) >= 1): ?>
                    <li class="border-b border-gray-200 py-2">
                        <a href="<?= site_url('pelanggan/pesanan/list') ?>" class="hover:text-gray-600 hover:underline">Anda memliki Tiket yang belum dikonfirmasi oleh admin.</a>
                    </li>
                    <?php endif; ?>
                    <?php if(count($listPemesanan['menunggu_pembayaran'] ) >= 1  || count($listPemesanan['belum_lunas']) >= 1): ?>
                    <li class="border-b border-gray-200 py-2">
                        <a href="<?= site_url('pelanggan/pesanan/menunggu-pembayaran') ?>" class="hover:text-gray-600 hover:underline">Anda memliki Tiket yang belum dilunasi, klik untuk melakukan pengecekan.</a>
                    </li>
                    <?php endif; ?>
                    <?php if(strtolower($me->getKategoriPelanggan()->getKategori()) == 'umum'): ?>
                    <li class="border-b border-gray-200 py-2">
                        <a href="<?= site_url('pelanggan/pengaturan/akun') ?>" class="hover:text-gray-600 hover:underline">Untuk mendapatkan diskon khusus Mahasiswa dan IMAPPEL silahkan atur disini.</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</main>
