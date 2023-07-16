<?php

if (false === session()->isAuthenticatedAs('admin')) html_unauthorized();

$listDaerahOperasional = app()->getManager()->getService('DaerahOperasionalService')->listDaerahOperasional();
$listProvinsi = Provinsi::toArray();

?>
<main x-data="container">
    <nav class="block w-full max-w-full bg-transparent text-white shadow-none transition-all px-0 py-1 border-b-2">
        <div class="flex flex-col-reverse justify-between gap-6 md:flex-row md:items-center">
            <h2 class="block antialiased tracking-normal font-sans text-2xl font-semibold leading-relaxed text-gray-900">Master - Daerah Operasional</h2>
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
                    <div class="relative bg-clip-border rounded-xl overflow-hidden bg-transparent text-gray-700 shadow-none m-0 flex items-center justify-between p-6">
                        <div>
                            <h6 class="block antialiased tracking-normal font-sans text-base font-semibold leading-relaxed text-gray-900 mb-1">Terdapat <span x-text="properties.data.list_daerah_operasional.length"></span> Daerah Operasional</h6>
                        </div>
                    </div>

                    <template x-if="properties.data.list_daerah_operasional.length > 0">
                    <div class="p-6 px-0 pt-0 pb-0">
                        <table class="w-full min-w-[640px] table-auto">
                            <thead>
                            <tr>
                                <th class="border-b border-gray-200 py-3 px-6 text-left w-2">
                                    <p class="block antialiased font-sans text-[11px] font-medium uppercase text-gray-400">No</p>
                                </th>
                                <th class="border-b border-gray-200 py-3 px-6 text-left">
                                    <p class="block antialiased font-sans text-[11px] font-medium uppercase text-gray-400">Nama Kota</p>
                                </th>
                                <th class="border-b border-gray-200 py-3 px-6 text-left">
                                    <p class="block antialiased font-sans text-[11px] font-medium uppercase text-gray-400">Provinsi</p>
                                </th>
                                <th class="border-b border-gray-20 py-3 px-6 text-left"></th>
                            </tr>
                            </thead>
                            <tbody>
                                    <template x-for="(daerah, index) in properties.data.list_daerah_operasional" :key="index">
                                        <tr>
                                            <td class="py-3 px-5 border-b border-gray-200 w-2">
                                                <p class="block antialiased font-sans text-sm leading-normal text-gray-600 text-center" x-text="(index + 1)"></p>
                                            </td>
                                            <td class="py-3 px-5 border-b border-gray-200">
                                                <p class="block antialiased font-sans text-xs font-medium text-gray-900 font-bold" x-text="daerah.nama_kota"></p>
                                            </td>
                                            <td class="py-3 px-5 border-b border-gray-200">
                                                <p class="block antialiased font-sans text-xs font-medium text-gray-900 font-bold" x-text="daerah.provinsi.nama"></p>
                                            </td>
                                            <td class="py-3 px-5 border-b border-gray-200 flex justify-end gap-2">
                                                <button class="bg-orange-400 text-white rounded px-4 py-1 font-sans center" @click="editData(daerah)">Edit</button>
                                                <button class="bg-red-500 text-white rounded px-4 py-1 font-sans center" @click="hapusData(daerah)">Hapus</button>
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
                            <h6 class="block antialiased tracking-normal font-sans text-base font-semibold leading-relaxed text-gray-900 mb-2"><span x-text="properties.sites.button_title"></span> Daerah Operasional</h6>
                        </div>
                        <div class="w-full min-w-[200px] mb-4">
                            <label for="" class="font-sans text-base text-gray-500 mb-2 block">Nama Kota <small x-text="properties.sites.query_title"></small></label>
                            <input type="text" x-model="properties.form.nama_kota" class="w-full bg-transparent text-gray-700 font-sans font-normal outline outline-0 border-2 text-sm px-3 py-3 rounded-md border-gray-200 focus:border-gray-400" autofocus>
                        </div>
                        <div class="w-full min-w-[200px] mb-4">
                            <label for="" class="font-sans text-base text-gray-500 mb-2 block">Provinsi</label>
                            <select x-model="properties.form.provinsi" class="w-full bg-transparent text-gray-700 font-sans font-normal outline outline-0 border-2 text-sm px-3 py-3 rounded-md border-gray-200 focus:border-gray-400">
                                <option value="-1">-- Pilih Provinsi --</option>
                                <template x-for="(provinsi, index) in properties.data.list_provinsi" :key="index">
                                    <option :value="index" x-text="provinsi"></option>
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
            "editData": function (daerah) {
                this.properties.form.nama_kota = daerah.nama_kota;
                this.properties.form.provinsi = daerah.provinsi.id;
                this.properties.form.id = daerah.id;

                this.properties.sites.query_title = `(dipilih: ${daerah.nama_kota})`;
                this.properties.sites.button_title = 'Perbaharui';
            },
            "simpanData": function () {
                this.clearMassage();

                let alpineObj = this;
                let elem = this.$event.target;
                let elemTitle = this.properties.sites.button_title;
                this.buttonLoading(elem);

                this.postData(
                    '/api/admin/daerah-operasional/simpan', 
                    this.createFormData({
                        'nama_kota': this.properties.form.nama_kota,
                        'provinsi': this.properties.form.provinsi,
                        'id': this.properties.form.id,
                    }),
                    function (response) {
                        elem.disabled = false;
                        elem.innerText = elemTitle;
                        elem.classList.remove('bg-gray-700');
                        elem.classList.remove('hover:bg-gray-700');
                        elem.classList.remove('focus:ring-gray-700');
                        elem.classList.remove('opacity-80');
                        elem.classList.remove('cursor-not-allowed');

                        if (alpineObj.properties.form.id < 0) {
                            alpineObj.properties.data.list_daerah_operasional.push({
                                'id': response.data.data.id,
                                'nama_kota': response.data.data.nama_kota,
                                'provinsi': response.data.data.provinsi
                            });

                            alpineObj.properties.form.nama_kota = null;
                            alpineObj.properties.form.provinsi = null;
                            alpineObj.properties.form.id = -1;

                            return;
                        }

                        let index = alpineObj.properties.data.list_daerah_operasional.findIndex(item => item.id == response.data.data.id);
                        alpineObj.properties.data.list_daerah_operasional[index].nama_kota = response.data.data.nama_kota;
                        alpineObj.properties.data.list_daerah_operasional[index].provinsi.id = response.data.data.provinsi.id;
                        alpineObj.properties.data.list_daerah_operasional[index].provinsi.nama = response.data.data.provinsi.nama;

                        alpineObj.addNormalMessage('form_response', 'Berhasil! Daerah operasional telah disimpan');
                    },
                    function (err) {
                        alpineObj.addErrorMassage('bad_request', 'Gagal dalam menyimpan data, mohon muat ulang halaman dan coba lagi.')
                    }
                )
            },
            "hapusData": function (daerah) {
                if(!confirm('Anda yakin ingin menghapus data ?')) return;

                this.clearMassage();

                let alpineObj = this;
                this.postData(
                    '/api/admin/daerah-operasional/hapus',
                    this.createFormData({
                        'id': daerah.id,
                    }),
                    function (response) {
                        let index = alpineObj.properties.data.list_daerah_operasional.findIndex(item => item.id == response.data.data.deleted_id);
                        alpineObj.properties.data.list_daerah_operasional.splice(index, 1);

                        alpineObj.addNormalMessage('form_response', 'Berhasil! Daerah operasional telah dihapus.');
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
                        "button_title": 'Tambahkan'
                    },
                    "messages": {
                        "errors": [],
                        "normal": []
                    },
                    "data": {
                        "list_daerah_operasional": JSON.parse('<?= json_encode(array_map(fn ($item) => $item->toArray(), $listDaerahOperasional)) ?>'),
                        "list_provinsi": JSON.parse('<?= json_encode($listProvinsi) ?>')
                    },
                    "form": {
                        'id' : -1,
                        'nama_kota': null,
                        'provinsi': null
                    }
                },
                "init": function() {}
            })
        );
    });
</script>