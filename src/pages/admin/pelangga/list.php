<?php

if (false === session()->isAuthenticatedAs('admin')) html_unauthorized();

if (isset($_GET['page'])) {
    if ($_GET['page'] <= 0) html_not_found();
}

$page = $_GET['page'] ?? 1;
$search = $_GET['search'] ?? null;
$listPelanggan = app()->getManager()->getService('PelangganService')->listPelanggan($page, $search);

?>
<main x-data="container">
    <nav class="block w-full max-w-full bg-transparent text-white shadow-none transition-all px-0 py-1 border-b-2">
        <div class="flex flex-col-reverse justify-between gap-6 md:flex-row md:items-center">
            <h2 class="block antialiased tracking-normal font-sans text-2xl font-semibold leading-relaxed text-gray-900">Data Pelanggan</h2>
            <div class="flex items-center gap-4">
                <span class="font-sans text-gray-500">Sekarang: <?php echo tanggal(date_create()) ?></span>
                <a @click="window.location.reload()" class="underline text-gray-500 hover:text-gray-600 cursor-pointer">Muat Ulang</a>
            </div>
        </div>
    </nav>
    <div id="content" class="mt-8 w-full overflow-hidden">
        <div class="mb-4 w-full flex justify-between">
            <form action="" method="get">
                <div class="flex justify-between">
                    <input type="search" name="search" class="block w-72 p-2 px-4 mr-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="Ketik nama pelanggan.." value="<?= $search ?? '' ?>" required>
                    <button type="submit" class="px-4 cursor-pointer text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm">Cari..</button>
                </div>
            </form>
        </div>
        <div>
            <!-- List Data -->
            <div class="mb-8">
                <h3 class="mb-4 block antialiased tracking-normal font-sans text-xl font-semibold leading-relaxed text-gray-900" x-text="properties.sites.query_title"></h3>
                <div class="bg-white rounded-lg shadow-md p-6 overflow-x-auto px-0 pt-2 pb-0">
                    <table class="w-full min-w-[640px] table-auto">
                        <thead>
                        <tr>
                            <th class="border-b border-gray-200 py-3 px-6 text-left text-center w-16">
                                <p class="block antialiased font-sans text-[11px] font-medium uppercase text-gray-400">No</p>
                            </th>
                            <th class="border-b border-gray-200 py-3 px-6 text-left text-center">
                                <p class="block antialiased font-sans text-[11px] font-medium uppercase text-gray-400">Nama Pelanggan</p>
                            </th>
                            <th class="border-b border-gray-200 py-3 px-6 text-left text-center">
                                <p class="block antialiased font-sans text-[11px] font-medium uppercase text-gray-400">Kontak</p>
                            </th>
                            <th class="border-b border-gray-200 py-3 px-6 text-left text-center">
                                <p class="block antialiased font-sans text-[11px] font-medium uppercase text-gray-400">Kategori Pelanggan</p>
                            </th>
                            <th class="border-b border-gray-20 py-3 px-6 text-left"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if(count($listPelanggan) < 1): ?>
                            <tr>
                                <td class="py-3 px-5 border-b border-gray-200 text-center" colspan="7">
                                    <p class="block antialiased font-sans text-xs font-medium text-gray-900 font-bold" >Tidak ada data.</p>
                                </td>
                            </tr>
                        <?php else:
                            /** @var $pelanggan Pelanggan */
                            foreach ($listPelanggan as $index => $pelanggan) :
                            ?>
                            <tr>
                                <td class="py-3 px-5 border-b border-gray-200">
                                    <p class="block antialiased font-sans text-sm leading-normal text-gray-900 font-bold text-center"><?= ($index + 1) ?></p>
                                </td>
                                <td class="py-3 px-5 border-b border-gray-200 text-center">
                                    <p class="block antialiased font-sans text-xs font-medium text-gray-600"><?= $pelanggan->getNama(); ?> (<?= $pelanggan->getAkun()->getUsername() ?>)</p>
                                </td>
                                <td class="py-3 px-5 border-b border-gray-200 text-center">
                                    <p class="block antialiased font-sans text-xs font-medium text-gray-600"><?= $pelanggan->getKontak(); ?></p>
                                </td>
                                <td class="py-3 px-5 border-b border-gray-200 text-center">
                                    <p class="block antialiased font-sans text-xs font-medium text-gray-600"><?= $pelanggan->getKategoriPelanggan()->getKategori() ?></p>
                                </td>
                                <td class="py-3 px-5 border-b border-gray-200 text-center">
                                    <p class="block antialiased font-sans text-xs font-medium text-gray-600"></p>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="flex justify-end">
                <?php if($page > 1): ?>
                <div class="px-4 py-2 bg-white rounded-tl-lg rounded-bl-lg shadow-lg w-32 hover:bg-gray-700 hover:text-white cursor-pointer">
                    <a href="?page=<?= $page - 1 ?>">Sebelumnya</a>
                </div>
                <?php endif; ?>
                <div class="px-4 py-2 bg-white rounded-tr-lg rounded-br-lg shadow-lg w-32 hover:bg-gray-700 hover:text-white cursor-pointer">
                    <a href="?page=<?= $page + 1 ?>">Selanjutnya</a>
                </div>
            </div>
        </div>
    </div>
</main>