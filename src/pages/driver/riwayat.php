<?php
if (false === session()->isAuthenticatedAs('driver')) html_unauthorized();

$page = $_GET['page'] ?? 1;

$app = app()->getManager();
$driver = $app->getService('DriverService')->findByAkun(session()->auth());
$listPesanan = $app->getService('PemesananService')->listRiwayatPesananByDriver($driver, $page);

?>
<main>
    <nav class="block w-full max-w-full bg-transparent text-white shadow-none transition-all px-0 py-1 border-b-2">
        <div class="flex flex-col-reverse justify-between gap-6 md:flex-row md:items-center">
            <h2 class="block antialiased tracking-normal font-sans text-2xl font-semibold leading-relaxed text-gray-900">Riwayat Perjalanan</h2>
            <div class="hidden sm:flex sm:items-center gap-4">
                <span class="font-sans text-gray-500">Sekarang: <?php echo tanggal(date_create()) ?></span>
            </div>
        </div>
    </nav>
    <div id="content" class="mt-8 w-full overflow-hidden">
        <div>
            <div class="mb-8">
                <div class="bg-white rounded-lg shadow-md p-6 overflow-x-auto px-0 pt-2 pb-0 mb-8">
                    <table class="w-full min-w-[640px] table-auto">
                        <thead>
                        <tr>
                            <th class="border-b border-gray-200 py-3 px-6 text-left text-center w-20">
                                <p class="block antialiased font-sans text-[11px] font-medium uppercase text-gray-400">Tanggal Keberangkatan</p>
                            </th>
                            <th class="border-b border-gray-200 py-3 px-6 text-left text-center w-56">
                                <p class="block antialiased font-sans text-[11px] font-medium uppercase text-gray-400">Jam Keberangkatan</p>
                            </th>
                            <th class="border-b border-gray-200 py-3 px-6 text-left text-center w-56">
                                <p class="block antialiased font-sans text-[11px] font-medium uppercase text-gray-400">Rute</p>
                            </th>
                            <th class="border-b border-gray-200 py-3 px-6 text-left text-center">
                                <p class="block antialiased font-sans text-[11px] font-medium uppercase text-gray-400">Mobil</p>
                            </th>
                            <th class="border-b border-gray-200 py-3 px-6 text-left text-center">
                                <p class="block antialiased font-sans text-[11px] font-medium uppercase text-gray-400">Jumlah Penumpang</p>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if($listPesanan):
                            foreach ($listPesanan as $pesanan) : /** @var $pesanan Pesanan */
                                ?>
                                <tr>
                                    <td class="py-3 px-5 border-b border-gray-200 w-48">
                                        <p class="block antialiased font-sans text-sm text-center leading-normal text-gray-600"><?= tanggal(date_create($pesanan['tanggal_keberangkatan'])) ?></p>
                                    </td>
                                    <td class="py-3 px-5 border-b border-gray-200 text-center">
                                        <p class="block antialiased font-sans text-xs font-medium text-gray-600"><?= $pesanan['jam_keberangkatan'] ?> WIB</p>
                                    </td>
                                    <td class="py-3 px-5 border-b border-gray-200 text-center">
                                        <p class="block antialiased font-sans text-xs font-medium text-gray-600"><?= $pesanan['kota_asal'] ?> - <?= $pesanan['kota_tujuan'] ?></p>
                                    </td>
                                    <td class="py-3 px-5 border-b border-gray-200 text-center">
                                        <p class="block antialiased font-sans text-xs font-medium text-gray-600"><?= $pesanan['mobil'] ?></p>
                                    </td>
                                    <td class="py-3 px-5 border-b border-gray-200 text-center">
                                        <p class="block antialiased font-sans text-xs font-medium text-gray-600"><?= $pesanan['jumlah_penumpang'] ?> kursi</p>
                                    </td>
                                </tr>
                            <?php endforeach;
                        else: ?>
                            <tr>
                                <td class="py-3 px-5 border-b border-gray-200 text-center" colspan="7">
                                    <p class="block antialiased font-sans text-xs font-medium text-gray-900 font-bold" >Tidak ada data.</p>
                                </td>
                            </tr>
                        <?php endif ?>
                        </tbody>
                    </table>
                </div>
                <div class="flex">
                    <?php if($page > 1): ?>
                        <div class="px-4 py-2 bg-white rounded shadow-md mr-2 shadow-lg w-32 hover:bg-gray-700 hover:text-white cursor-pointer">
                            <a href="?page=<?= $page - 1 ?>">Sebelumnya</a>
                        </div>
                    <?php endif; ?>
                    <div class="px-4 py-2 bg-white rounded shadow-md w-32 hover:bg-gray-700 hover:text-white cursor-pointer">
                        <a href="?page=<?= $page + 1 ?>">Selanjutnya</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>