<main x-data="container">
    <nav class="block w-full max-w-full bg-transparent text-white shadow-none transition-all px-0 py-1 border-b-2">
        <div class="flex flex-col-reverse justify-between gap-6 md:flex-row md:items-center">
            <h2 class="block antialiased tracking-normal font-sans text-2xl font-semibold leading-relaxed text-gray-900" x-text="properties.sites.page_title"></h2>
            <div class="flex items-center">
                <span class="font-sans text-gray-500"><?php echo tanggal(date_create()) ?></span>
            </div>
        </div>
    </nav>
    <div id="content" class="mt-8 w-full overflow-hidden">
        <div class="mb-4 w-full flex justify-between">
            <div class="w-4/12">
                <label for="search" class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Search</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg aria-hidden="true" class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input x-model="properties.form.filter_search" type="search" id="search" class="block w-full p-4 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="Ketik..." required>
                    <button type="button" @click="loadListPesananByNomorPesanan" class="cursor-pointer text-white absolute right-2.5 bottom-2.5 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2">Cari Nomor Pesanan..</button>
                </div>
            </div>
            <div class="w-4/12">
                <label for="search" class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Search</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg aria-hidden="true" class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input x-model="properties.form.filter_tanggal" type="text" @change="console.log($event)" name="filter-tanggal" class="block w-full p-4 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="Search" required>
                    <button type="button" @click="loadListPesananByDate" class="cursor-pointer text-white absolute right-2.5 bottom-2.5 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2">Cari Tanggal Keberangkatan..</button>
                </div>
            </div>
        </div>
        <div>
            <!-- List Pesanan Hari Ini -->
            <div class="mb-8">
                <h3 class="mb-4 block antialiased tracking-normal font-sans text-xl font-semibold leading-relaxed text-gray-900" x-text="properties.sites.query_prefix"></h3>
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
                            <template x-if="properties.data.pesanan.length < 1">
                                <tr>
                                    <td class="py-3 px-5 border-b border-gray-200 text-center" colspan="7">
                                        <p class="block antialiased font-sans text-xs font-medium text-gray-900 font-bold" >Tidak ada data.</p>
                                    </td>
                                </tr>
                            </template>
                            <template x-for="pesanan in properties.data.pesanan" x-else>
                                <tr>
                                    <td class="py-3 px-5 border-b border-gray-200">
                                        <p class="block antialiased font-sans text-sm leading-normal text-gray-900 font-bold" x-text="pesanan.data.pesanan.nomor"></p>
                                    </td>
                                    <td class="py-3 px-5 border-b border-gray-200 text-center">
                                        <p class="block antialiased font-sans text-xs font-medium text-gray-600" x-text="pesanan.data.pesanan.jam_keberangkatan + ' WIB'"></p>
                                    </td>
                                    <td class="py-3 px-5 border-b border-gray-200 text-center">
                                        <p class="block antialiased font-sans text-xs font-medium text-gray-600" x-text="pesanan.data.rute.full"></p>
                                    </td>
                                    <td class="py-3 px-5 border-b border-gray-200 text-center">
                                        <p class="block antialiased font-sans text-xs font-medium text-gray-600" x-text="pesanan.data.pemesan.nama + ' (' + pesanan.data.pemesan.kontak + ')'"></p>
                                    </td>
                                    <td class="py-3 px-5 border-b border-gray-200 text-center">
                                        <p class="inline antialiased font-sans text-xs text-white py-1 px-2 rounded" :class="'bg-' + pesanan.data.pesanan.status_pemesanan.color + '-400'" x-text="pesanan.data.pesanan.status_pemesanan.value"></p>
                                    </td>
                                    <td class="py-3 px-5 border-b border-gray-200 text-center">
                                        <p class="inline antialiased font-sans text-xs text-white py-1 px-2 rounded" :class="'bg-' + pesanan.data.pesanan.status_pembayaran.color + '-400'" x-text="pesanan.data.pesanan.status_pembayaran.display"></p>
                                    </td>
                                    <td class="py-3 px-5 border-b border-gray-200">
                                        <div class="flex gap-2 justify-end">
                                            <a href="" class="text-sm text-red-500">Konfirmasi Pembayaran</a> |
                                            <a :href="'<?= site_url('admin/pesanan/detail?nomor=') ?>' + pesanan.data.pesanan.nomor" class="text-sm text-red-500 cursor-pointer">Lihat Detail</a>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<script type="text/javascript">
    document.addEventListener('alpine:init', () => {
        // @TODO: separate to file
        const actions = {
            "loadListPesananByDate": async function() {
                this.properties.sites.query_prefix = 'Tanggal Keberangkatan: ' + this.properties.form.filter_tanggal;

                let filterTanggal = document.querySelector('input[name="filter-tanggal"]');
                if (filterTanggal.value != '' ) {
                    this.properties.form.filter_tanggal = document.querySelector('input[name="filter-tanggal"]').value;
                }

                let wanted = (new Date(this.properties.form.filter_tanggal)).toISOString().split('T')[0];

                this.properties.data.pesanan = await axios
                    .get(this.properties.sites.api_url + `/api/admin/pesanan?tanggal=${wanted}`)
                    .then(res => res.data.data)
                    .catch(err => console.error(err));
            },
            "loadListPesananByNomorPesanan": async function() {
                this.properties.sites.query_prefix = 'Pencarian: ' + this.properties.form.filter_search;

                this.properties.data.pesanan = await axios
                    .get(this.properties.sites.api_url + `/api/admin/pesanan?nomor=${this.properties.form.filter_search}`)
                    .then(res => res.data.data)
                    .catch(err => console.error(err));
            }
        };

        const utils = {
            "dateToSupportedFormat": function (date) {
                return (new Date(date)).toISOString().split('T')[0];
            }
        };

        Alpine.data('container',
            () => ({
                ...actions,
                ...utils,
                "properties": {
                    "sites": {
                        "api_url": "<?= site_url() ?>",
                        "page_title": "Keberangkatan",
                        "query_title": "Tanggal Keberangkatan: "
                    },
                    "errors": {},
                    "data": {
                        "tanggal_hari_ini": null,
                        "pesanan": []
                    },
                    "form": {
                        "filter_tanggal": null,
                        "filter_search": null
                    }
                },
                "init": function() {
                    this.properties.form.filter_tanggal = this.dateToSupportedFormat('<?= date("Y-m-d") ?>');
                    this.loadListPesananByDate();

                    const elem = document.querySelector('input[name="filter-tanggal"]');
                    const datepicker = new Datepicker(elem, {
                        autohide: true,
                        format: "yyyy-mm-dd"
                    });
                }
            })
        );
    });
</script>