<?php
if (false === session()->isAuthenticatedAs('pelanggan')) html_unauthorized();

$filter = [
    'page' => $_GET['page'] ?? 1,
    'search' => isset($_GET['search']) ? urldecode($_GET['search']) : null,
    'date' => $_GET['tanggal'] ?? null
];

$listPesanan = app()->getManager()->getService('PelangganService')->semuaPesananSaya($filter);

?>
<main>
        <nav class="block w-full max-w-full bg-transparent text-white shadow-none transition-all px-0 py-1 border-b-2">
            <div class="flex flex-col-reverse justify-between gap-6 md:flex-row md:items-center">
                <h2 class="block antialiased tracking-normal font-sans text-2xl font-semibold leading-relaxed text-gray-900">Semua Pemesanan</h2>
                <div class="flex items-center gap-4">
                    <span class="font-sans text-gray-500">Sekarang: <?php echo tanggal(date_create()) ?></span>
                    <a href="<?= site_url(get_current_route()) ?>" class="underline text-gray-500 hover:text-gray-600 cursor-pointer">Reset Filter</a>
                </div>
            </div>
        </nav>
        <div id="content" class="mt-8 w-full overflow-hidden">
        <div class="mb-4 w-full flex justify-between">
            <form action="" method="get">
                <div class="flex justify-start w-full">
                <div class="p-2">Pencarian: </div>
                <div class="flex justify-between">
                    <input name="search" type="search" id="search" class="block w-72 p-2 px-4 mr-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="Ketik nomor tiket...">
                    <button type="submit" class="px-4 cursor-pointer text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm">Cari..</button>
                </div>
            </div>
            </form>
            <form action="" method="get">
                <div class="flex justify-end w-full">
                    <div class="p-2">Tanggal Keberangkatan: </div>
                    <div class="mr-2">
                        <input name="tanggal" type="date" id="search"  class="w-64 cursor-pointer p-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <button type="submit" class="cursor-pointer text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2">Cari..</button>
                    </div>
                </div>
            </form>
        </div>
        <div>
            <div class="mb-8">
                <div class="bg-white rounded-lg shadow-md p-6 overflow-x-auto px-0 pt-2 pb-0 mb-8">
                    <table class="w-full min-w-[640px] table-auto">
                        <thead>
                        <tr>
                            <th class="border-b border-gray-200 py-3 px-6 text-left text-center w-32">
                                <p class="block antialiased font-sans text-[11px] font-medium uppercase text-gray-400">Nomor Pesanan</p>
                            </th>
                            <th class="border-b border-gray-200 py-3 px-6 text-left text-center w-20">
                                <p class="block antialiased font-sans text-[11px] font-medium uppercase text-gray-400">Tanggal Pemesanan</p>
                            </th>
                            <th class="border-b border-gray-200 py-3 px-6 text-left text-center w-20">
                                <p class="block antialiased font-sans text-[11px] font-medium uppercase text-gray-400">Tanggal Keberangkatan</p>
                            </th>
                            <th class="border-b border-gray-200 py-3 px-6 text-left text-center w-56">
                                <p class="block antialiased font-sans text-[11px] font-medium uppercase text-gray-400">Rute</p>
                            </th>
                            <th class="border-b border-gray-200 py-3 px-6 text-left text-center">
                                <p class="block antialiased font-sans text-[11px] font-medium uppercase text-gray-400">Total Tagihan</p>
                            </th>
                            <th class="border-b border-gray-200 py-3 px-6 text-left text-center">
                                <p class="block antialiased font-sans text-[11px] font-medium uppercase text-gray-400">Dibayar</p>
                            </th>
                            <th class="border-b border-gray-20 py-3 px-6 text-left"></th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php if($listPesanan):
                                foreach ($listPesanan as $pesanan) : /** @var $pesanan Pesanan */
                                ?>
                            <tr>
                                <td class="py-3 px-5 border-b border-gray-200">
                                        <p class="block antialiased font-sans text-sm leading-normal text-gray-900 font-bold"><?= $pesanan->getNomorPesanan() ?></p>
                                    </td>
                                    <td class="py-3 px-5 border-b border-gray-200 text-center">
                                        <p class="block antialiased font-sans text-xs font-medium text-gray-600"><?= tanggal($pesanan->getTanggalPemesanan(), false, true)  ?> WIB</p>
                                    </td>
                                    <td class="py-3 px-5 border-b border-gray-200 text-center">
                                        <p class="block antialiased font-sans text-xs font-medium text-gray-600"><?= tanggal($pesanan->getTanggalKeberangkatan()) ?></p>
                                    </td>
                                    <td class="py-3 px-5 border-b border-gray-200 text-center">
                                        <p class="block antialiased font-sans text-xs font-medium text-gray-600"><?= $pesanan->getRute() ?></p>
                                    </td>
                                    <td class="py-3 px-5 border-b border-gray-200 text-center">
                                        <p class="inline antialiased font-sans text-xs py-1 px-2 rounded">Rp <?= rupiah($pesanan->getTotalTarif()) ?></p>
                                    </td>
                                    <td class="py-3 px-5 border-b border-gray-200 text-center">
                                        <?php $lunas = $pesanan->getTotalTarif() <= $pesanan->getTotalDibayarkan(); ?>
                                        <p class="inline antialiased font-sans text-xs py-1 px-2 rounded text-white bg-<?= $lunas ? 'green': 'yellow' ?>-500">
                                            <?=  $lunas ? 'Lunas' : 'Sisa: Rp' . rupiah($pesanan->getTotalTarif() - $pesanan->getTotalDibayarkan()) ?>
                                        </p>
                                    </td>
                                    <td class="py-3 px-5 border-b border-gray-200">
                                        <div class="flex gap-2 justify-end">
                                           <a href="'<?= site_url('admin/pesanan/detail?nomor=') ?>'" class="text-sm text-red-500 cursor-pointer">Lihat Detail</a>
                                        </div>
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
                    <?php if($filter['page'] > 1): ?>
                        <div class="px-4 py-2 bg-white rounded shadow-md mr-2 shadow-lg w-32 hover:bg-gray-700 hover:text-white cursor-pointer">
                            <a href="?page=<?= $filter['page'] - 1 ?>">Sebelumnya</a>
                        </div>
                    <?php endif; ?>
                    <div class="px-4 py-2 bg-white rounded shadow-md w-32 hover:bg-gray-700 hover:text-white cursor-pointer">
                        <a href="?page=<?= $filter['page'] + 1 ?>">Selanjutnya</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>