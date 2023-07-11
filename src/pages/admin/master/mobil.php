<?php

if (false === session()->isAuthenticatedAs('admin')) html_unauthorized();
$listDriver = app()->getManager()->getService('DriverService')->listDriver(20);
$listMobil = app()->getManager()->getService('MobilService')->listMobil();
?>
<main x-data="container">
    <nav class="block w-full max-w-full bg-transparent text-white shadow-none transition-all px-0 py-1 border-b-2">
        <div class="flex flex-col-reverse justify-between gap-6 md:flex-row md:items-center">
            <h2 class="block antialiased tracking-normal font-sans text-2xl font-semibold leading-relaxed text-gray-900">Master - Data Mobil Operasional</h2>
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
                <div class="flex flex-col bg-clip-border rounded-xl bg-white text-gray-700 shadow-md overflow-hidden">
                    <template x-if="properties.data.list_mobil.length > 0">
                        <div class="p-6 px-0 pt-0 pb-0">
                            <table class="w-full min-w-[640px] table-auto">
                                <thead>
                                <tr>
                                    <th class="border-b border-gray-200 py-3 px-6 text-left w-2">
                                        <p class="block antialiased font-sans text-[11px] font-medium uppercase text-gray-400">No</p>
                                    </th>
                                    <th class="border-b border-gray-200 py-3 px-6 text-left">
                                        <p class="block antialiased font-sans text-[11px] font-medium uppercase text-gray-400">Merk Mobil</p>
                                    </th>
                                    <th class="border-b border-gray-200 py-3 px-6 text-left">
                                        <p class="block antialiased font-sans text-[11px] font-medium uppercase text-gray-400">Plat Nomor</p>
                                    </th>
                                    <th class="border-b border-gray-200 py-3 px-6 text-left">
                                        <p class="block antialiased font-sans text-[11px] font-medium uppercase text-gray-400">Jumlah Kursi Penumpang</p>
                                    </th>
                                    <th class="border-b border-gray-200 py-3 px-6 text-left">
                                        <p class="block antialiased font-sans text-[11px] font-medium uppercase text-gray-400">Driver</p>
                                    </th>
                                    <th class="border-b border-gray-20 py-3 px-6 text-left"></th>
                                </tr>
                                </thead>
                                <tbody>
                                <template x-for="(item, index) in properties.data.list_mobil" :key="index">
                                    <tr>
                                        <td class="py-3 px-5 border-b border-gray-200 w-2">
                                            <p class="block antialiased font-sans text-sm leading-normal text-gray-600 text-center" x-text="(index + 1)"></p>
                                        </td>
                                        <td class="py-3 px-5 border-b border-gray-200">
                                            <p class="block antialiased font-sans text-xs font-medium text-gray-900 font-bold" x-text="item.merk"></p>
                                        </td>
                                        <td class="py-3 px-5 border-b border-gray-200">
                                            <p class="block antialiased font-sans text-xs font-medium text-gray-900 font-bold" x-text="item.plat_nomor"></p>
                                        </td>
                                        <td class="py-3 px-5 border-b border-gray-200">
                                            <p class="block antialiased font-sans text-xs font-medium text-gray-900 font-bold" x-text="item.jumlah_kursi + ' kursi'"></p>
                                        </td>
                                        <td class="py-3 px-5 border-b border-gray-200">
                                            <p class="block antialiased font-sans text-xs font-medium text-gray-900 font-bold" x-text="item.driver.nama"></p>
                                        </td>
                                        <td class="py-3 px-5 border-b border-gray-200 flex justify-end gap-2">
                                            <button class="bg-orange-400 text-white rounded px-4 py-1 font-sans center" @click="editData(item)">Edit</button>
                                            <button class="bg-red-500 text-white rounded px-4 py-1 font-sans center" @click="hapusData(item)">Hapus</button>
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
                        <div class="relative bg-clip-border rounded-xl overflow-hidden bg-transparent text-gray-700 shadow-none m-0">
                            <h6 class="block antialiased tracking-normal font-sans text-base font-semibold leading-relaxed text-gray-900 mb-2"><span x-text="properties.sites.button_title"></span> Mobil</h6>
                        </div>
                        <div class="w-full min-w-[200px] mb-4">
                            <label for="" class="font-sans text-base text-gray-500 mb-2 block">Merk <small class="text-gray-500" x-text="properties.sites.query_title"></small></label>
                            <input type="text" x-model="properties.form.merk" class="w-full bg-transparent text-gray-700 font-sans font-normal outline outline-0 border-2 text-sm px-3 py-3 rounded-md border-gray-200 focus:border-gray-400" autofocus>
                        </div>
                        <div class="w-full min-w-[200px] mb-4">
                            <label for="" class="font-sans text-base text-gray-500 mb-2 block">Plat Nomor</label>
                            <input type="text" x-model="properties.form.plat_nomor" class="w-full bg-transparent text-gray-700 font-sans font-normal outline outline-0 border-2 text-sm px-3 py-3 rounded-md border-gray-200 focus:border-gray-400">
                        </div>
                        <div class="w-full min-w-[200px] mb-4">
                            <label for="" class="font-sans text-base text-gray-500 mb-2 block">Jumlah Kursi Penumpang</label>
                            <input type="number" x-model="properties.form.jumlah_kursi" class="w-full bg-transparent text-gray-700 font-sans font-normal outline outline-0 border-2 text-sm px-3 py-3 rounded-md border-gray-200 focus:border-gray-400">
                        </div>
                        <div class="w-full min-w-[200px] mb-4">
                            <label for="" class="font-sans text-base text-gray-500 mb-2 block">Driver</label>
                            <select x-model="properties.form.driver" class="w-full bg-transparent text-gray-700 font-sans font-normal outline outline-0 border-2 text-sm px-3 py-3 rounded-md border-gray-200 focus:border-gray-400">
                                <option value="-1">-- Pilih Driver --</option>
                                <template x-for="(driver, index) in properties.data.list_driver" :key="index">
                                    <option :value="driver.id" x-text="driver.nama"></option>
                                </template>
                            </select>
                        </div>
                        <div class="w-full min-w-[200px]">
                            <button @click="simpanData" type="submit" class="bg-green-500 w-full text-white rounded py-4 font-sans center" x-text="properties.sites.button_title"></button>
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
            "editData": function (mobil) {
                this.properties.form.jumlah_kursi = mobil.jumlah_kursi;
                this.properties.form.merk = mobil.merk;
                this.properties.form.id = mobil.id;
                this.properties.form.driver = mobil.driver.id;
                this.properties.form.plat_nomor = mobil.plat_nomor;

                this.properties.sites.query_title = `(dipilih: ${mobil.merk})`;
                this.properties.sites.button_title = 'Perbaharui';
            },
            "simpanData": function () {
                this.clearMassage();

                let alpineObj = this;
                let elem = this.$event.target;
                let elemTitle = this.properties.sites.button_title;
                this.buttonLoading(elem);

                this.postData(
                    '/api/admin/mobil/simpan',
                    this.createFormData({
                        'id': this.properties.form.id,
                        'merk': this.properties.form.merk,
                        'jumlah_kursi': this.properties.form.jumlah_kursi,
                        'driver': this.properties.form.driver,
                        'plat_nomor': this.properties.form.plat_nomor
                    }),
                    function (response) {
                        elem.disabled = false;
                        elem.innerText = elemTitle;
                        elem.classList.remove('bg-gray-700');
                        elem.classList.remove('hover:bg-gray-700');
                        elem.classList.remove('focus:ring-gray-700');
                        elem.classList.remove('opacity-80');
                        elem.classList.remove('cursor-not-allowed');

                        if (alpineObj.properties.form.id < 0) { // saved
                            alpineObj.properties.data.list_mobil.push(response.data.data);

                            alpineObj.properties.form.jumlah_kursi = "";
                            alpineObj.properties.form.merk = "";
                            alpineObj.properties.form.id = -1;
                            alpineObj.properties.form.driver = -1;
                            alpineObj.properties.form.plat_nomor = "";

                            alpineObj.addNormalMessage('form_response', `Berhasil! Mobil ${response.data.data.merk} ${response.data.data.plat_nomor} (Driver: ${response.data.data.driver.nama}) telah disimpan.`);

                            return;
                        }

                        // updated
                        let index = alpineObj.properties.data.list_mobil.findIndex(item => item.id == response.data.data.id);
                        alpineObj.properties.data.list_mobil[index].merk = response.data.data.merk;
                        alpineObj.properties.data.list_mobil[index].plat_nomor = response.data.data.plat_nomor;
                        alpineObj.properties.data.list_mobil[index].jumlah_kursi = response.data.data.jumlah_kursi;
                        alpineObj.properties.data.list_mobil[index].driver.nama = response.data.data.driver.nama;
                        alpineObj.properties.data.list_mobil[index].driver.kontak = response.data.data.driver.kontak;
                        alpineObj.properties.data.list_mobil[index].driver.id = response.data.data.driver.id;

                        alpineObj.addNormalMessage('form_response', `Berhasil! Driver ${response.data.data.merk} ${response.data.data.plat_nomor} (Driver: ${response.data.data.driver.nama}) telah diperbaharui.`);
                    },
                    function (err) {
                        alpineObj.addErrorMassage('bad_request', 'Gagal dalam menyimpan, mohon periksa data dan coba lagi.')
                    }
                )
            },
            "hapusData": function (entity) {
                if(!confirm('Anda yakin ingin menghapus driver ?')) return;

                this.clearMassage();

                let alpineObj = this;
                this.postData(
                    '/api/admin/mobil/hapus',
                    this.createFormData({
                        'id': entity.id,
                    }),
                    function (response) {
                        let index = alpineObj.properties.data.list_mobil.findIndex(item => item.id == response.data.data.deleted_id);
                        alpineObj.properties.data.list_mobil.splice(index, 1);

                        alpineObj.addNormalMessage('form_response', 'Berhasil! Data driver telah dihapus.');
                    },
                    function (err) {
                        alpineObj.addErrorMassage('bad_request', 'Gagal menghapus data drivet, mohon muat ulang halaman dan coba lagi.')
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
                        "page_title": "Daerah Operasional",
                        "query_title": null,
                        "button_title": 'Tambahkan',
                        "show_password_input": true
                    },
                    "messages": {
                        "errors": [],
                        "normal": []
                    },
                    "data": {
                        "list_mobil": JSON.parse('<?= json_encode(array_map(fn ($item) => $item->toArray(), $listMobil)) ?>'),
                        "list_driver": JSON.parse('<?= json_encode(array_map(fn ($item) => $item->toArray(), $listDriver)) ?>')
                    },
                    "form": {
                        'id' : -1,
                        'merk': '',
                        'jumlah_kursi': '',
                        'plat_nomor': '',
                        'driver': -1,
                    }
                },
                "init": function() {}
            })
        );
    });
</script>