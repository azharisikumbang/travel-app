<?php

if (false === session()->isAuthenticatedAs('admin')) html_unauthorized();
$listTarif = app()->getManager()->getService('TiketService')->listTarif(100);
$listKategoriPelanggan = app()->getManager()->getService('KategoriPelangganService')->listKategoriPelanggan();
$listRute = app()->getManager()->getService('RuteService')->listRute(50);

?>
<main x-data="container">
    <nav class="block w-full max-w-full text-white shadow-none transition-all px-0 py-1 border-b-2">
        <div class="flex flex-col-reverse justify-between gap-6 md:flex-row md:items-center">
            <h2 class="block antialiased tracking-normal font-sans text-2xl font-semibold leading-relaxed text-gray-900">Master - Rute Tiket Tersedia</h2>
            <div class="flex items-center gap-4">
                <span class="font-sans text-gray-500 underline">Sekarang: <?php echo tanggal(date_create()) ?></span>
                <span class="text-gray-500 underline hover:text-opacity-75 cursor-pointer" @click="window.location.reload()">Muat Ulang</span>
            </div>
        </div>
    </nav>
    <div id="content" class="mt-8 w-full overflow-hidden">
        <?php if(session('temp')): ?>
            <div class="mb-4 block w-full text-base font-regular px-4 py-4 rounded-lg bg-green-500 text-white">
                <?php echo session('temp')['message'] ?>
            </div>
        <?php endif; ?>
        <template x-if="properties.messages.errors">
            <template x-for="error in properties.messages.errors">
                <?php html_alert("error.message", "error.color"); ?>
            </template>
        </template>
        <template x-if="properties.messages.normal">
            <template x-for="normal in properties.messages.normal">
                <?php html_alert("normal.message", "normal.color"); ?>
            </template>
        </template>
        <div class="mb-4 grid grid-cols-1 gap-6 xl:grid-cols-3">
            <div class="xl:col-span-2">
                <div class="flex flex-col bg-clip-border rounded-xl bg-white text-gray-700 shadow-md">
                    <div class="flex items-center justify-between p-6">
                        <div class="flex justify-between w-full items-center">
                            <div>
                                <h6 class="block antialiased tracking-normal font-sans text-base font-semibold leading-relaxed text-gray-900 mb-1">Menampilkan Tarif Per Kategori Penumpang</h6>
                            </div>
                            <div>
                                <span>Tampilkan : </span>
                                <select x-model="properties.form.filter.kategori" class="cursor-pointer text-gray-700 font-sans font-normal outline outline-0 border-2 text-sm px-3 py-3 rounded-md border-gray-200 focus:border-gray-400">
                                    <option value="-1">-- Semua Ketegori --</option>
                                    <template x-for="(entity, index) in properties.data.list_kategori_penumpang" :key="index">
                                        <option :value="entity.id" x-text="entity.kategori"></option>
                                    </template>
                                </select>
                            </div>
                        </div>
                    </div>

                    <template x-if="properties.data.list_tarif.length > 0">
                        <div class="p-6 px-0 pt-0 pb-0 table-wrp block max-h-screen">
                            <table class="w-full min-w-[640px] table-auto">
                                <thead class="bg-white border-b sticky top-0">
                                    <tr class="text-center">
                                        <th class="border-b border-gray-200 py-3 px-6">
                                            <p class="block antialiased font-sans text-[11px] font-medium uppercase text-gray-400">Kategori</p>
                                        </th>
                                        <th class="border-b border-gray-200 py-3 px-6 text-left">
                                            <p class="block antialiased font-sans text-[11px] font-medium uppercase text-gray-400">Rute</p>
                                        </th>
                                        <th class="border-b border-gray-200 py-3 px-6">
                                            <p class="block antialiased font-sans text-[11px] font-medium uppercase text-gray-400">Nominal Tarif</p>
                                        </th>
                                        <th class="border-b border-gray-20 py-3 px-6"></th>
                                    </tr>
                                </thead>
                                <tbody class="h-96 overflow-y-auto scrollbar">
                                <template x-for="(entity, index) in properties.data.list_tarif" :key="index">
                                    <tr class="text-center" x-show="isFiltered(entity.kategori.id)">
                                        <td class="py-3 px-5 border-b border-gray-200">
                                            <p class="block antialiased font-sans text-xs font-medium text-gray-900 font-bold" x-text="entity.kategori.kategori"></p>
                                        </td>
                                        <td class="py-3 px-5 border-b border-gray-200 text-left">
                                            <p class="block antialiased font-sans text-xs font-medium text-gray-900 font-bold" x-text="entity.rute.asal.nama_kota + ' - ' + entity.rute.tujuan.nama_kota"></p>
                                        </td>
                                        <td class="py-3 px-5 border-b border-gray-200">
                                            <p class="block antialiased font-sans text-xs font-medium text-gray-900 font-bold" x-text="rupiah(entity.tarif)"></p>
                                        </td>
                                        <td class="py-3 px-5 border-b border-gray-200 flex justify-end gap-2">
                                            <button class="bg-yellow-500 text-white rounded px-4 py-1 font-sans center hover:bg-yellow-600 trigger-edit" @click="editData(entity)">Edit</button>
                                            <button class="bg-red-500 text-white rounded px-4 py-1 font-sans center hover:bg-red-600" @click="hapusData(entity)">Hapus</button>
                                        </td>
                                    </tr>
                                </template>
                                </tbody>
                            </table>
                        </div>
                    </template>
                </div>
            </div>
            <div>
                <form @submit.prevent="simpanData">
                    <div class="flex flex-col bg-clip-border rounded-xl bg-white text-gray-700 shadow-md p-6">
                        <div class="relative bg-clip-border rounded-xl overflow-hidden text-gray-700 shadow-none m-0">
                            <h6 class="block antialiased tracking-normal font-sans text-base font-semibold leading-relaxed text-gray-900 mb-2"><span x-text="properties.sites.button_title"></span> Rute Tiket</h6>
                        </div>
                        <div class="w-full min-w-[200px] mb-4">
                            <label for="" class="font-sans text-base text-gray-500 mb-2 block">Kategori Penumpang</label>
                            <select x-model="properties.form.kategori" class="w-full cursor-pointer text-gray-700 font-sans font-normal outline outline-0 border-2 text-sm px-3 py-3 rounded-md border-gray-200 focus:border-gray-400" required>
                                <option value="-1">-- Pilih Kategori --</option>
                                <template x-for="(entity, index) in properties.data.list_kategori_penumpang" :key="index">
                                    <option :value="entity.id" x-text="entity.kategori"></option>
                                </template>
                            </select>
                        </div>
                        <div class="w-full min-w-[200px] mb-4">
                            <label for="" class="font-sans text-base text-gray-500 mb-2 block">Rute Perjalanan</label>
                            <select x-model="properties.form.rute" class="w-full cursor-pointer text-gray-700 font-sans font-normal outline outline-0 border-2 text-sm px-3 py-3 rounded-md border-gray-200 focus:border-gray-400" required>
                                <option value="-1">-- Pilih Rute --</option>
                                <template x-for="(entity, index) in properties.data.list_rute" :key="index">
                                    <option :value="entity.id" x-text="entity.asal.nama_kota + ' - ' + entity.tujuan.nama_kota"></option>
                                </template>
                            </select>
                        </div>
                        <div class="w-full min-w-[200px] mb-4">
                            <label for="" class="font-sans text-base text-gray-500 mb-2 block">Nominal Tarif (Rupiah)</label>
                            <input type="number" x-model="properties.form.tarif" class="w-full text-gray-700 font-sans font-normal outline outline-0 border-2 text-sm px-3 py-3 rounded-md border-gray-200 focus:border-gray-400">
                        </div>
                        <div class="w-full min-w-[200px]">
                            <button @click="simpanData" type="submit" class="bg-green-500 w-full text-white rounded py-4 font-sans center hover:bg-green-600" x-text="properties.sites.button_title"></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
<script type="text/javascript">
    document.addEventListener('alpine:init', () => {
        // @TODO: separate to file
        const actions = {
            "cari": function () {
                alert('not-implemented');
            },
            "isFiltered": function (kategoriID) {
                return kategoriID == this.properties.form.filter.kategori || this.properties.form.filter.kategori == -1;
            },
            "editData": function (entity) {
                let listTriggerEdit = document.getElementsByClassName('trigger-edit');
                for (const item of listTriggerEdit) {
                    item.textContent = 'Edit';
                    item.classList.remove('bg-gray-500');
                    item.classList.remove('hover:bg-gray-500');
                    item.classList.remove('cursor-not-allowed');
                }

                this.$event.target.textContent = 'Dipilih..';
                this.$event.target.classList.add('bg-gray-500');
                this.$event.target.classList.add('hover:bg-gray-500');
                this.$event.target.classList.add('cursor-not-allowed');

                this.properties.form.id = entity.id;
                this.properties.form.rute = entity.rute.id;
                this.properties.form.kategori = entity.kategori.id;
                this.properties.form.tarif = entity.tarif;

                // this.properties.sites.query_title = `(dipilih: ${entity.jam})`;
                this.properties.sites.button_title = 'Perbaharui';
            },
            "simpanData": function () {
                this.clearMassage();

                let alpineObj = this;
                let elem = this.$event.target;
                let elemTitle = this.properties.sites.button_title;
                this.buttonLoading(elem);

                alpineObj.properties.form.filter.kategori = -1;

                this.postData(
                    '/api/admin/tiket/simpan',
                    this.createFormData({
                        'id': this.properties.form.id,
                        'kategori': this.properties.form.kategori,
                        'tarif': this.properties.form.tarif,
                        'rute': this.properties.form.rute
                    }),
                    function (response) {
                        elem.disabled = false;
                        elem.innerText = elemTitle;
                        elem.classList.remove('bg-gray-700');
                        elem.classList.remove('hover:bg-gray-700');
                        elem.classList.remove('focus:ring-gray-700');
                        elem.classList.remove('opacity-80');
                        elem.classList.remove('cursor-not-allowed');

                        let listTriggerEdit = document.getElementsByClassName('trigger-edit');
                        for (const item of listTriggerEdit) {
                            item.textContent = 'Edit';
                            item.classList.remove('bg-gray-500');
                            item.classList.remove('hover:bg-gray-500');
                            item.classList.remove('cursor-not-allowed');
                        }

                        alpineObj.properties.form.rute = -1;
                        alpineObj.properties.form.kategori = -1;
                        alpineObj.properties.form.tarif = 0;
                        alpineObj.properties.form.id = -1;
                        alpineObj.properties.sites.button_title = 'Tambahkan';

                        alpineObj.properties.form.filter.kategori = response.data.data.kategori.id;

                        if (alpineObj.properties.form.id < 0) {
                            alpineObj.properties.data.list_tarif.push({
                                'id': response.data.data.id,
                                'rute': response.data.data.rute,
                                'kategori': response.data.data.kategori,
                                'tarif': response.data.data.tarif
                            });

                            return;
                        }

                        let index = alpineObj.properties.data.list_tarif.findIndex(item => item.id == response.data.data.id);
                        alpineObj.properties.data.list_tarif[index].kategori = response.data.data.kategori;
                        alpineObj.properties.data.list_tarif[index].rute = response.data.data.rute;
                        alpineObj.properties.data.list_tarif[index].tarif = response.data.data.tarif;

                        for (const item of listTriggerEdit) {
                            item.textContent = 'Edit';
                            item.classList.remove('bg-gray-500');
                            item.classList.remove('hover:bg-gray-500');
                            item.classList.remove('cursor-not-allowed');
                        }

                        alpineObj.addNormalMessage('form_response', `Berhasil! Tiket rute (${response.data.data.rute.asal.nama_kota} / ${response.data.data.rute.tujuan.nama_kota}) telah ${elemTitle}.`);
                    },
                    function (err) {
                        console.error(err);
                        alpineObj.addErrorMassage('bad_request', 'Gagal dalam menyimpan data, mohon muat ulang halaman dan coba lagi.')
                    }
                )
            },
            "hapusData": function (entity) {
                if(!confirm('Anda yakin ingin menghapus data ?')) return;

                this.clearMassage();

                let alpineObj = this;
                this.postData(
                    '/api/admin/tiket/hapus',
                    this.createFormData({
                        'id': entity.id,
                    }),
                    function (response) {
                        let index = alpineObj.properties.data.list_tarif.findIndex(item => item.id == response.data.data.deleted_id);
                        alpineObj.properties.data.list_tarif.splice(index, 1);

                        alpineObj.addNormalMessage('form_response', 'Berhasil! Kategori pelanggan telah dihapus.');
                    },
                    function (err) {
                        alpineObj.addErrorMassage('bad_request', 'Gagal menghapus data data, mohon muat ulang halaman dan coba lagi.')
                    }
                )
            }
        };

        const utils = {
            "tanggalToIndo": function (tanggal) {
                if (!tanggal) return null;
                let date = new Date(tanggal);

                return date.toLocaleDateString('id-ID',  { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
            },
            "rupiah": function (number) {
                return 'Rp ' + (new Intl.NumberFormat('id-Id', {"maximumSignificantDigits": 3}).format(number));
            },
            "buttonLoading": function(elem, statusText = 'Mohon Tunggu') {
                elem.disabled = true;
                elem.innerText = statusText;
                elem.classList.add('bg-gray-700');
                elem.classList.add('hover:bg-gray-700');
                elem.classList.add('focus:ring-gray-700');
                elem.classList.add('opacity-80');
                elem.classList.add('cursor-not-allowed');
            },
            "buttonRemoveLoading": function (elem, statusText, success = 'bg-green-700') {
                elem.disabled = false;
                elem.innerText = statusText;
                elem.classList.remove('bg-gray-700');
                elem.classList.remove('hover:bg-gray-700');
                elem.classList.remove('focus:ring-gray-700');
                elem.classList.remove('opacity-80');
                elem.classList.remove('cursor-not-allowed');

                elem.classList.add('bg-green-700');
            },
            "getApiRequest": function (to, params = null) {
                return axios
                    .get(this.properties.sites.api_url + to, { params: params })
                    .then(res => res.data)
                    .catch(err => console.log(err));
            },
            "postData": function (to, data, callback, callbackError) {
                let that = this;
                return axios
                    .post(this.properties.sites.api_url + to, data)
                    .then(res => callback(res))
                    .catch(err => callbackError(err));
            },
            "createFormData": function (data) {
                const form = new FormData();
                for (const key in data) form.append(key, data[key]);

                return form;
            },
            "addErrorMassage": function (name, message) {
                this.addMessage(name, message);
            },
            "addNormalMessage": function (name, message) {
                this.addMessage(name, message, 'normal', 'green');
            },
            "addMessage": function (name, message, container = 'errors', color = 'red') {
                let exists = this.properties.messages[container].findIndex(item => item.name == name);

                if (exists != -1) {
                    this.properties.messages[container][exists] = { 'name': name, 'message': message, 'color': color };

                    return;
                }

                this.properties.messages[container].push({ 'name': name, 'message': message, 'color': color });
            },
            "clearMassage": function(){
                this.properties.messages.errors = [];
                this.properties.messages.normal = [];
            }
        };

        Alpine.data('container',
            () => ({
                ...actions,
                ...utils,
                "properties": {
                    "sites": {
                        "api_url": "<?= site_url() ?>",
                        "page_title": "Tiket",
                        "query_title": null,
                        "button_title": 'Tambahkan'
                    },
                    "messages": {
                        "errors": [],
                        "normal": []
                    },
                    "data": {
                        "list_tarif": JSON.parse('<?= json_encode(array_map(fn ($item) => $item->toArray(), $listTarif)) ?>'),
                        "list_kategori_penumpang": JSON.parse('<?= json_encode(array_map(fn ($item) => $item->toArray(), $listKategoriPelanggan)) ?>'),
                        "list_rute": JSON.parse('<?= json_encode(array_map(fn ($item) => $item->toArray(), $listRute)) ?>')
                    },
                    "form": {
                        'id' : -1,
                        'kategori': -1,
                        'rute': -1,
                        'tarif': 0,
                        'filter': {
                            'kategori': -1
                        }
                    }
                },
                "init": function() {}
            })
        );
    });
</script>