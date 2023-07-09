<main x-data="container">
    <nav class="block w-full max-w-full bg-transparent text-white shadow-none transition-all px-0 py-1 border-b-2">
        <div class="flex flex-col-reverse justify-between gap-6 md:flex-row md:items-center">
            <h2 class="block antialiased tracking-normal font-sans text-2xl font-semibold leading-relaxed text-gray-900" x-text="properties.sites.page_title"></h2>
            <div class="flex items-center gap-4">
                <span class="font-sans text-gray-500">Sekarang: <?php echo tanggal(date_create()) ?></span>
                <a @click="window.location.reload()" class="underline text-gray-500 hover:text-gray-600 cursor-pointer">Muat Ulang</a>
            </div>
        </div>
    </nav>
    <div id="content" class="mt-8 w-full overflow-hidden">
        <div class="mb-4 w-full flex justify-between">
            <div class="">
                <label for="search" class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Cari..</label>
                <div class="flex justify-between">
                    <input x-model="properties.form.filter_search" type="search" id="search" class="block w-72 p-2 px-4 mr-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="Ketik..." required>
                    <button type="button" @click="loadListPesananByNomorPesanan" class="px-4 cursor-pointer text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm">Cari Nomor Pesanan..</button>
                </div>
            </div>
            <div class="w-8/12">
                <label for="search" class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Search</label>
                <div class="relative">
                    <div class="absolute flex justify-end w-full">
                        <div class="p-2">Saring :</div>
                        <div class="mr-4">
                            <select x-model="properties.form.jadwal" class="cursor-pointer p-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                <option value="_">-- Semua Jadwal --</option>
                                <template x-for="jadwal in properties.data.filter.jadwal">
                                    <option :value="jadwal.value" x-text="jadwal.text"></option>
                                </template>
                            </select>
                        </div>
                        <div class="mr-4">
                            <select x-model="properties.form.asal" class="cursor-pointer p-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                <option value="-1">-- Semua Kota Asal --</option>
                                <template x-for="kota in properties.data.filter.asal">
                                    <option :value="kota.value" x-text="kota.text"></option>
                                </template>
                            </select>
                        </div>
                        <div class="mr-4">
                            <select x-model="properties.form.tujuan" class="cursor-pointer p-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                <option value="-1">-- Semua Kota Tujuan --</option>
                                <template x-for="kota in properties.data.filter.tujuan">
                                    <option :value="kota.value" x-text="kota.text"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <button type="button" @click="filterListPesanan" class="cursor-pointer text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2">Cari..</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div>
            <!-- List Pesanan Hari Ini -->
            <div class="mb-8">
                <h3 class="mb-4 block antialiased tracking-normal font-sans text-xl font-semibold leading-relaxed text-gray-900" x-text="properties.sites.query_title"></h3>
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
                                    <p class="block antialiased font-sans text-xs font-medium text-gray-600" x-text="pesanan.data.pesanan.jadwal_lengkap"></p>
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
                                        <a href="" class="text-sm text-red-500">Lihat Detail</a>
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
            "filterListPesanan": async function() {
                if (this.$event != undefined) this.buttonLoading(this.$event.target);

                this.properties.data.pesanan = await axios
                    .get(this.properties.sites.api_url + `/api/admin/jadwal?tanggal=${this.properties.form.jadwal}&asal=${this.properties.form.asal}&tujuan=${this.properties.form.tujuan}`)
                    .then(res => {
                        if (this.$event != undefined) this.buttonRemoveLoading(this.$event.target, 'Cari..');

                        return res.data.data;
                    })
                    .catch(err => console.error(err));
            },
            "loadListPesananByNomorPesanan": async function() {
                this.buttonLoading(this.$event.target);
                this.properties.sites.query_title = 'Pencarian: ' + this.properties.form.filter_search;

                this.properties.data.pesanan = await axios
                    .get(this.properties.sites.api_url + `/api/admin/pesanan?nomor=${this.properties.form.filter_search}&pembayaran=1`)
                    .then(res => {
                        this.buttonRemoveLoading(this.$event.target, 'Cari Nomor Pesanan..');

                        return res.data.data;
                    })
                    .catch(err => console.error(err));
            },
            "loadDaerahOperasional": async function () {
                let data = await axios
                    .get(this.properties.sites.api_url + `/api/list-daerah-operasional`)
                    .then(res => {
                        return res.data.data.map((item) => {
                            return { "value": item.id, "text": item.nama_kota  }
                        });
                    })
                    .catch(err => console.error(err));

                this.properties.data.filter.asal = data;
                this.properties.data.filter.tujuan = data;
            }
        };

        const utils = {
            "dateToSupportedFormat": function (date) {
                return (new Date(date)).toISOString().split('T')[0];
            },
            "buttonLoading": function(elem, statusText = 'Mohon Tunggu') {
                elem.disabled = true;
                elem.innerText = statusText;
                elem.classList.add('bg-gray-700');
                elem.classList.add('hover:bg-gray-700');
                elem.classList.add('focus:ring-gray-700');
                elem.classList.add('opacity-50');
                elem.classList.add('cursor-not-allowed');
            },
            "buttonRemoveLoading": function (elem, statusText) {
                elem.disabled = false;
                elem.innerText = statusText;
                elem.classList.remove('bg-gray-700');
                elem.classList.remove('hover:bg-gray-700');
                elem.classList.remove('focus:ring-gray-700');
                elem.classList.remove('opacity-50');
                elem.classList.remove('cursor-not-allowed');
            }
        };

        Alpine.data('container',
            () => ({
                ...actions,
                ...utils,
                "properties": {
                    "sites": {
                        "api_url": "<?= site_url() ?>",
                        "page_title": "Jadwal Keberangkatan",
                        "query_title": null
                    },
                    "errors": {},
                    "data": {
                        "pesanan": [],
                        "tanggal_hari_ini": null,
                        "filter": {
                            "jadwal": [
                                { "value": "today", "text": "Jadwal Hari Ini." },
                                { "value": "+1 days", "text": "Jadwal Besok." },
                                { "value": "+2 days", "text": "Jadwal Lusa." },
                                { "value": "+7 days", "text": "Jadwal Minggu Ini." },
                            ],
                            "asal": [],
                            "tujuan": []
                        }
                    },
                    "form": {
                        "filter_tanggal": null,
                        "filter_search": null,
                        "jadwal": 'today',
                        "asal": -1,
                        "tujuan": -1
                    }
                },
                "init": function() {
                    this.loadDaerahOperasional();
                    this.filterListPesanan();
                }
            })
        );
    });
</script>