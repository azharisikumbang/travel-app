<?php

/** @var $pesanan Pesanan */
$listPesanan = app()->getManager()->getService('PemesananService')->listPesananBerdasarkanHari(20, 0);

?>
<main x-data="container">
    <nav class="block w-full max-w-full bg-transparent text-white shadow-none transition-all px-0 py-1 border-b-2">
        <div class="flex flex-col-reverse justify-between gap-6 md:flex-row md:items-center">
            <h2 class="block antialiased tracking-normal font-sans text-2xl font-semibold leading-relaxed text-gray-900">Keberangkatan</h2>
            <div class="flex items-center">
                <span class="font-sans text-gray-500"><?php echo tanggal(date_create()) ?></span>
            </div>
        </div>
    </nav>
    <div id="content" class="mt-8 w-full overflow-hidden">
        <?php if(session('temp')): ?>
            <div class="mb-4 block w-full text-base font-regular px-4 py-4 rounded-lg bg-green-500 text-white">
                <?php echo session('temp')['message'] ?>
            </div>
        <?php endif; ?>
        <div class="mb-4 w-full flex justify-between">
            <div class="w-4/12">
                <form action="" method="get">
                    <label for="search" class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Search</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg aria-hidden="true" class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="search" id="search" class="block w-full p-4 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="Ketik..." required>
                        <button type="submit" class="text-white absolute right-2.5 bottom-2.5 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2">Cari</button>
                    </div>
                </form>
            </div>
            <div class="w-4/12">
                <label for="search" class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Search</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg aria-hidden="true" class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" name="filter-tanggal" class="block w-full p-4 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="Search" required>
                    <button type="submit" class="text-white absolute right-2.5 bottom-2.5 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2">Cari..</button>
                </div>
            </div>
        </div>
        <div>
            <!-- List Pesanan Hari Ini -->
            <div class="mb-8">
                <h3 class="mb-4 block antialiased tracking-normal font-sans text-xl font-semibold leading-relaxed text-gray-900" id="tanggalHariIni"></h3>
                <div class="bg-white rounded-lg shadow-md p-6 overflow-x-auto px-0 pt-2 pb-0">
                    <table class="w-full min-w-[640px] table-auto">
                        <thead>
                        <tr>
                            <th class="border-b border-gray-200 py-3 px-6 text-left text-center w-32">
                                <p class="block antialiased font-sans text-[11px] font-medium uppercase text-gray-400">Nomor Pesanan</p>
                            </th>
                            <th class="border-b border-gray-200 py-3 px-6 text-left text-center w-20">
                                <p class="block antialiased font-sans text-[11px] font-medium uppercase text-gray-400">Jam Keberangkatan</p>
                            </th>
                            <th class="border-b border-gray-200 py-3 px-6 text-left text-center w-56">
                                <p class="block antialiased font-sans text-[11px] font-medium uppercase text-gray-400">Rute</p>
                            </th>
                            <th class="border-b border-gray-200 py-3 px-6 text-left text-center">
                                <p class="block antialiased font-sans text-[11px] font-medium uppercase text-gray-400">Pemesan</p>
                            </th>
                            <th class="border-b border-gray-200 py-3 px-6 text-left text-center w-48">
                                <p class="block antialiased font-sans text-[11px] font-medium uppercase text-gray-400">Status Pemesanan</p>
                            </th>
                            <th class="border-b border-gray-200 py-3 px-6 text-left text-center w-56">
                                <p class="block antialiased font-sans text-[11px] font-medium uppercase text-gray-400">Status Pembayaran</p>
                            </th>
                            <th class="border-b border-gray-20 py-3 px-6 text-left"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        if (count($listPesanan['hari_ini']) < 1) { ?>
                            <tr>
                                <td class="py-3 px-5 border-b border-gray-200 text-center" colspan="7">
                                    <p class="block antialiased font-sans text-xs font-medium text-gray-900 font-bold" >Tidak ada data.</p>
                                </td>
                            </tr>
                            <?php
                        } else {
                            foreach ($listPesanan['hari_ini'] as $pesanan): ?>
                                <tr>
                                    <td class="py-3 px-5 border-b border-gray-200">
                                        <p class="block antialiased font-sans text-sm leading-normal text-gray-900 font-bold"><?= $pesanan->getNomorPesanan(); ?></p>
                                    </td>
                                    <td class="py-3 px-5 border-b border-gray-200 text-center">
                                        <p class="block antialiased font-sans text-xs font-medium text-gray-600"><?= $pesanan->getJamKeberangkatan(); ?></p>
                                    </td>
                                    <td class="py-3 px-5 border-b border-gray-200 text-center">
                                        <p class="block antialiased font-sans text-xs font-medium text-gray-600"><?= $pesanan->getRute(); ?></p>
                                    </td>
                                    <td class="py-3 px-5 border-b border-gray-200 text-center">
                                        <p class="block antialiased font-sans text-xs font-medium text-gray-600"><?= $pesanan->getNamaPemesan(); ?> (<?= $pesanan->getKontakPemesan(); ?>)</p>
                                    </td>
                                    <td class="py-3 px-5 border-b border-gray-200 text-center">
                                        <p class="inline antialiased font-sans text-xs text-white bg-<?= $pesanan->getStatusPemesanan()->getColor() ?>-400 py-1 px-2 rounded"><?= $pesanan->getStatusPemesanan()->value; ?></p>
                                    </td>
                                    <td class="py-3 px-5 border-b border-gray-200 text-center">
                                        <p class="inline antialiased font-sans text-xs text-white bg-<?= $pesanan->getStatusBuktiPembayaran()->getColor() ?>-400 py-1 px-2 rounded"><?= $pesanan->getStatusBuktiPembayaran()->getDisplayName(); ?></p>
                                    </td>
                                    <td class="py-3 px-5 border-b border-gray-200">
                                        <div class="flex gap-2 justify-end">
                                            <a href="" class="text-sm text-red-500">Konfirmasi Pembayaran</a> |
                                            <a href="" class="text-sm text-red-500">Lihat Detail</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php
                            endforeach;
                        } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- List Pesanan Lainnya -->
            <div class="mb-8">
                <h3 class="mb-4 block antialiased tracking-normal font-sans text-xl font-semibold leading-relaxed text-gray-900" id="tanggalHariIni"></h3>
                <div class="bg-white rounded-lg shadow-md p-6 overflow-x-auto px-0 pt-2 pb-0">
                    <table class="w-full min-w-[640px] table-auto">
                        <thead>
                        <tr>
                            <th class="border-b border-gray-200 py-3 px-6 text-left text-center w-32">
                                <p class="block antialiased font-sans text-[11px] font-medium uppercase text-gray-400">Nomor Pesanan</p>
                            </th>
                            <th class="border-b border-gray-200 py-3 px-6 text-left text-center w-20">
                                <p class="block antialiased font-sans text-[11px] font-medium uppercase text-gray-400">Jadwal Keberangkatan</p>
                            </th>
                            <th class="border-b border-gray-200 py-3 px-6 text-left text-center w-56">
                                <p class="block antialiased font-sans text-[11px] font-medium uppercase text-gray-400">Rute</p>
                            </th>
                            <th class="border-b border-gray-200 py-3 px-6 text-left text-center">
                                <p class="block antialiased font-sans text-[11px] font-medium uppercase text-gray-400">Pemesan</p>
                            </th>
                            <th class="border-b border-gray-200 py-3 px-6 text-left text-center w-48">
                                <p class="block antialiased font-sans text-[11px] font-medium uppercase text-gray-400">Status Pemesanan</p>
                            </th>
                            <th class="border-b border-gray-200 py-3 px-6 text-left text-center w-56">
                                <p class="block antialiased font-sans text-[11px] font-medium uppercase text-gray-400">Status Pembayaran</p>
                            </th>
                            <th class="border-b border-gray-20 py-3 px-6 text-left"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        if (count($listPesanan['lainnya']) < 1) { ?>
                            <tr>
                                <td class="py-3 px-5 border-b border-gray-200 text-center" colspan="7">
                                    <p class="block antialiased font-sans text-xs font-medium text-gray-900 font-bold" >Tidak ada data.</p>
                                </td>
                            </tr>
                            <?php
                        } else {
                            foreach ($listPesanan['lainnya'] as $pesanan): ?>
                                <tr>
                                    <td class="py-3 px-5 border-b border-gray-200">
                                        <p class="block antialiased font-sans text-sm leading-normal text-gray-900 font-bold"><?= $pesanan->getNomorPesanan(); ?></p>
                                    </td>
                                    <td class="py-3 px-5 border-b border-gray-200 text-center">
                                        <p class="block antialiased font-sans text-xs font-medium text-gray-600"><?= $pesanan->getJadwalLengkap(); ?></p>
                                    </td>
                                    <td class="py-3 px-5 border-b border-gray-200 text-center">
                                        <p class="block antialiased font-sans text-xs font-medium text-gray-600"><?= $pesanan->getRute(); ?></p>
                                    </td>
                                    <td class="py-3 px-5 border-b border-gray-200 text-center">
                                        <p class="block antialiased font-sans text-xs font-medium text-gray-600"><?= $pesanan->getNamaPemesan(); ?> (<?= $pesanan->getKontakPemesan(); ?>)</p>
                                    </td>
                                    <td class="py-3 px-5 border-b border-gray-200 text-center">
                                        <p class="inline antialiased font-sans text-xs text-white bg-<?= $pesanan->getStatusPemesanan()->getColor() ?>-400 py-1 px-2 rounded"><?= $pesanan->getStatusPemesanan()->value; ?></p>
                                    </td>
                                    <td class="py-3 px-5 border-b border-gray-200 text-center">
                                        <p class="inline antialiased font-sans text-xs text-white bg-<?= $pesanan->getStatusBuktiPembayaran()->getColor() ?>-400 py-1 px-2 rounded"><?= $pesanan->getStatusBuktiPembayaran()->getDisplayName(); ?></p>
                                    </td>
                                    <td class="py-3 px-5 border-b border-gray-200">
                                        <div class="flex gap-2 justify-end">
                                            <a href="" class="text-sm text-red-500">Konfirmasi Pembayaran</a> |
                                            <a href="" class="text-sm text-red-500">Lihat Detail</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php
                            endforeach;
                        } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<script type="text/javascript">

    function parseTanggalToIndo(tanggal) {
        if (!tanggal) return null;
        let date = new Date(tanggal);

        return date.toLocaleDateString('id-ID',  { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
    }

    function displayTodayDate()
    {
        let tanggal = parseTanggalToIndo(new Date());

        document.getElementById("tanggalHariIni").innerText = 'Hari Ini / ' + tanggal;

    }

    document.addEventListener('alpine:init', () => {
        // @TODO: separate to file
        const actions = {
            "getListPesananByDate": function(tanggal) {

            }
        };

        const utils = {

        };

        Alpine.data('container',
            () => ({
                ...actions,
                ...utils,
                "properties": {
                    "sites": {
                        "api_url": "<?= site_url() ?>",
                        "page_title": "Keberangkatan"
                    },
                    "errors": {},
                    "data": {
                        "tanggal_hari_ini": null,
                        "pesanan": []
                    },
                    "form": {
                        ""
                    }
                },
                "init": function() {
                    const today = new Date();
                    this.properties.data.pesanan = this.getListPesananByDate(today);
                }
            })
        );
    });

</script>