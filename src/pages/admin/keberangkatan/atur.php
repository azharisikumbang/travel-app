<?php

if (false === session()->isAuthenticatedAs('admin')) html_unauthorized();

$manager = app()->getManager();
$listKeberangkatanHarian = $manager->getService('KeberangkatanService')->listKeberangkatanHarian();
$listMobil = $manager->getService('MobilService')->listMobil(50);
$listJamKeberangkatan = $manager->getService('JamKeberangkatanService')->listJamKeberangkatan();
$listRute = $manager->getService('RuteService')->listRute(50);

?>
<main x-data="container">
    <nav class="block w-full max-w-full text-white shadow-none transition-all px-0 py-1 border-b-2">
        <div class="flex flex-col-reverse justify-between gap-6 md:flex-row md:items-center">
            <h2 class="block antialiased tracking-normal font-sans text-2xl font-semibold leading-relaxed text-gray-900">Master - Atur Keberangkatan Harian</h2>
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
        <div class="mb-4 grid grid-cols-1 gap-6">
            <div>
                <div class="flex flex-col bg-clip-border rounded-xl bg-white text-gray-700 shadow-md">
                    <div class="flex items-center justify-between p-6">
                        <div class="flex justify-between w-full items-center">
                            <div>
                                <h6 class="block antialiased tracking-normal font-sans text-base font-semibold leading-relaxed text-gray-900 mb-1">Menampilkan Keberangkatan Per Merk Mobil</h6>
                            </div>
                            <div>
                                <span>Tampilkan : </span>
                                <select x-model="properties.form.filter.mobil" class="cursor-pointer text-gray-700 font-sans font-normal outline outline-0 border-2 text-sm px-3 py-3 rounded-md border-gray-200 focus:border-gray-400">
                                    <option value="-1">-- Semua Merk Mobil --</option>
                                    <template x-for="(entity, index) in properties.data.list_mobil" :key="index">
                                        <option :value="entity.id" x-text="entity.merk + ' ' + entity.plat_nomor + ' / ' + entity.driver.nama"></option>
                                    </template>
                                </select>
                            </div>
                        </div>
                    </div>

                    <template x-if="properties.data.list_rute.length > 0">
                        <div class="p-6 px-0 pt-0 pb-0 table-wrp block max-h-screen">
                            <table class="w-full min-w-[640px] table-auto">
                                <thead class="bg-white border-b sticky top-0">
                                <tr class="text-center">
                                    <th class="border-b border-gray-200 py-3 px-6 w-96 text-left">
                                        <p class="block antialiased font-sans text-[11px] font-medium uppercase text-gray-400">Mobil</p>
                                    </th>
                                    <th class="border-b border-gray-200 py-3 px-6 text-left">
                                        <p class="block antialiased font-sans text-[11px] font-medium uppercase text-gray-400">Rute Perjalanan</p>
                                    </th>
                                    <th class="border-b border-gray-200 py-3 px-6">
                                        <p class="block antialiased font-sans text-[11px] font-medium uppercase text-gray-400">Jam Keberangkatan</p>
                                    </th>
                                    <th class="border-b border-gray-200 py-3 px-6">
                                        <p class="block antialiased font-sans text-[11px] font-medium uppercase text-gray-400 text-right">Terakhir Diperbaharui</p>
                                    </th>
                                    <th class="border-b border-gray-20 py-3 px-6 w-12"></th>
                                </tr>
                                </thead>
                                <tbody class="h-96 overflow-y-auto scrollbar">
                                <template x-for="(entity, index) in properties.data.list_keberangkatan" :key="index">
                                    <tr class="text-center" x-show="isFiltered(entity.mobil.id)">
                                        <td class="py-3 px-5 border-b border-gray-200 text-left align-top">
                                            <p class="block antialiased font-sans text-xs font-medium text-gray-900 font-bold" x-text="entity.mobil.merk + ' ' + entity.mobil.plat_nomor + ' (driver: ' + entity.mobil.driver.nama + ')'"></p>
                                        </td>
                                        <td class="py-3 px-5 border-b border-gray-200 text-left">
                                            <template x-for="rute in properties.data.list_rute">
                                                <div>
                                                    <input type="checkbox" x-model="properties.form.keberangkatan[index].rute" :value="rute.id" class="cursor-pointer" :checked="isRuteChecked(rute.id, entity.rute)">
                                                    <span x-text="rute.asal.nama_kota + ' - ' + rute.tujuan.nama_kota"></span>
                                                </div>
                                            </template>
                                        </td>
                                        <td class="py-3 px-5 border-b border-gray-200 align-top w-64">
                                            <select x-model="properties.form.keberangkatan[index].jam_keberangkatan" class="cursor-pointer w-full bg-transparent text-gray-700 font-sans font-normal outline outline-0 border-2 text-sm px-3 py-3 rounded-md border-gray-200 focus:border-gray-400">
                                                <option value="-1">-- Pilih Jam Keberangkatan --</option>
                                                <template x-for="jam in properties.data.list_jam_keberangkatan">
                                                    <option :value="jam.id" x-text="jam.jam + ' ( ' + jam.alias + ' )'" :selected="isJamKeberangkatanSelected(jam.id, entity.jam_keberangkatan)"></option>
                                                </template>
                                            </select>
                                        </td>
                                        <td class="py-3 px-5 border-b border-gray-200 text-right align-top">
                                            <p class="block antialiased font-sans text-xs font-medium text-gray-900 font-bold" x-text="tanggalToIndo(entity.last_updated)"></p>
                                        </td>
                                        <td class="py-3 px-5 border-t border-gray-200 flex justify-end gap-2">
                                            <button class="bg-green-500 text-white rounded px-4 py-1 font-sans center hover:bg-green-600 trigger-edit" @click="editData(entity, index)">Perbaharui</button>
                                            <button class="bg-red-500 text-white rounded px-4 py-1 font-sans center hover:bg-red-600 trigger-edit" @click="resetData(entity, index)">Reset</button>
                                        </td>
                                    </tr>
                                </template>
                                </tbody>
                            </table>
                        </div>
                    </template>
                </div>
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
            "isFiltered": function (id) {
                console.log(id);
                return id == this.properties.form.filter.mobil || this.properties.form.filter.mobil == -1;
            },
            "isRuteChecked": function (search, items) {
                for (const item of items)
                    if (item.id == search) return true;

                return false;

            },
            "isJamKeberangkatanSelected": function (search, jamKeberangkatan) {
                if (jamKeberangkatan == null) return false;

                return search == jamKeberangkatan.id;
            },
            "editData": function (entity, index) {
                // this.properties.form.keberangkatan[index]
                this.clearMassage();

                let alpineObj = this;
                let elem = this.$event.target;
                let elemTitle = this.properties.sites.button_title;
                this.buttonLoading(elem);

                this.postData(
                    '/api/admin/keberangkatan/simpan',
                    this.createFormData({
                        'rute': this.properties.form.keberangkatan[index].rute,
                        'jam_keberangkatan': this.properties.form.keberangkatan[index].jam_keberangkatan,
                        'mobil': this.properties.form.keberangkatan[index].mobil,
                    }),
                    function (response) {
                        elem.disabled = false;
                        elem.innerText = 'Perbaharui';
                        elem.classList.remove('bg-gray-700');
                        elem.classList.remove('hover:bg-gray-700');
                        elem.classList.remove('focus:ring-gray-700');
                        elem.classList.remove('opacity-80');
                        elem.classList.remove('cursor-not-allowed');

                        alpineObj.addNormalMessage('form_response', `Berhasil! Rute mobil telah telah diperbaharui.`);
                    },
                    function (err) {
                        alpineObj.addErrorMassage('bad_request', 'Gagal dalam menyimpan data, mohon muat ulang halaman dan coba lagi.')
                    }
                )
            },
            "resetData": function (entity, index) {
                if(!confirm('Anda yakin ingin mereset rute mobil ?')) return;

                this.clearMassage();

                let alpineObj = this;
                this.postData(
                    '/api/admin/keberangkatan/reset',
                    this.createFormData({
                        'mobil': this.properties.form.keberangkatan[index].mobil,
                    }),
                    function (response) {
                        alpineObj.properties.form.keberangkatan[index].rute = [];
                        alpineObj.properties.form.keberangkatan[index].jam_keberangkatan = -1;

                        alpineObj.addNormalMessage('form_response', 'Berhasil! Rute mobil telah dihapus telah reset.');
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
                        "page_title": "Keberangkatan",
                    },
                    "messages": {
                        "errors": [],
                        "normal": []
                    },
                    "data": {
                        "list_keberangkatan": JSON.parse('<?= json_encode($listKeberangkatanHarian) ?>'),
                        "list_mobil": JSON.parse('<?= json_encode(array_map(fn ($item) => $item->toArray(), $listMobil)) ?>'),
                        "list_jam_keberangkatan": JSON.parse('<?= json_encode(array_map(fn ($item) => $item->toArray(), $listJamKeberangkatan)) ?>'),
                        "list_rute": JSON.parse('<?= json_encode(array_map(fn ($item) => $item->toArray(), $listRute)) ?>')
                    },
                    "form": {
                        'keberangkatan': [],
                        'filter': {
                            'mobil': -1
                        }
                    }
                },
                "init": function() {
                    this.properties.data.list_keberangkatan.map((item, index) => {
                        let initJamKeberangkatan = null;
                        if (item.jam_keberangkatan != null) initJamKeberangkatan = item.jam_keberangkatan.id;


                        this.properties.form.keberangkatan.push({
                            'rute': item.rute.map(r => r.id),
                            'jam_keberangkatan': initJamKeberangkatan,
                            'mobil': item.mobil.id
                        });
                    });

                    console.log(this.properties.form.keberangkatan);
                }
            })
        );
    });
</script>