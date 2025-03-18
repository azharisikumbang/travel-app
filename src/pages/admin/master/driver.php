<?php

if (false === session()->isAuthenticatedAs('admin'))
    html_unauthorized();
$listDriver = app()->getManager()->getService('DriverService')->listDriver(20);

?>
<main x-data="container">
    <nav class="block w-full max-w-full bg-transparent text-white shadow-none transition-all px-0 py-1 border-b-2">
        <div class="flex flex-col-reverse justify-between gap-6 md:flex-row md:items-center">
            <h2
                class="block antialiased tracking-normal font-sans text-2xl font-semibold leading-relaxed text-gray-900">
                Master - Data Driver</h2>
            <div class="flex items-center gap-4">
                <span class="font-sans text-gray-500 underline">Sekarang: <?php echo tanggal(date_create()) ?></span>
                <span class="text-gray-500 underline hover:text-opacity-75 cursor-pointer"
                    @click="window.location.reload()">Muat Ulang</span>
            </div>
        </div>
    </nav>
    <div id="content" class="mt-8 w-full overflow-hidden">
        <?php if (session('temp')): ?>
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

                    <div
                        class="relative bg-clip-border rounded-xl overflow-hidden bg-transparent text-gray-700 shadow-none m-0 flex items-center justify-between p-6">
                        <div>
                            <h6
                                class="block antialiased tracking-normal font-sans text-base font-semibold leading-relaxed text-gray-900 mb-1">
                                Terdapat <span x-text="properties.data.list_driver.length"></span> Data Driver</h6>
                        </div>
                    </div>

                    <template x-if="properties.data.list_driver.length > 0">
                        <div class="p-6 px-0 pt-0 pb-0">
                            <table class="w-full min-w-[640px] table-auto">
                                <thead>
                                    <tr>
                                        <th class="border-b border-gray-200 py-3 px-6 text-left w-2">
                                            <p
                                                class="block antialiased font-sans text-[11px] font-medium uppercase text-gray-400">
                                                No</p>
                                        </th>
                                        <th class="border-b border-gray-200 py-3 px-6 text-left">
                                            <p
                                                class="block antialiased font-sans text-[11px] font-medium uppercase text-gray-400">
                                                Nama</p>
                                        </th>
                                        <th class="border-b border-gray-200 py-3 px-6 text-left">
                                            <p
                                                class="block antialiased font-sans text-[11px] font-medium uppercase text-gray-400">
                                                Kontak</p>
                                        </th>
                                        <th class="border-b border-gray-200 py-3 px-6 text-left">
                                            <p
                                                class="block antialiased font-sans text-[11px] font-medium uppercase text-gray-400">
                                                Akun</p>
                                        </th>
                                        <th class="border-b border-gray-20 py-3 px-6 text-left"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(driver, index) in properties.data.list_driver" :key="index">
                                        <tr>
                                            <td class="py-3 px-5 border-b border-gray-200 w-2">
                                                <p class="block antialiased font-sans text-sm leading-normal text-gray-600 text-center"
                                                    x-text="(index + 1)"></p>
                                            </td>
                                            <td class="py-3 px-5 border-b border-gray-200">
                                                <p class="block antialiased font-sans text-xs font-medium text-gray-900 font-bold"
                                                    x-text="driver.nama"></p>
                                            </td>
                                            <td class="py-3 px-5 border-b border-gray-200">
                                                <p class="block antialiased font-sans text-xs font-medium text-gray-900 font-bold"
                                                    x-text="driver.kontak"></p>
                                            </td>
                                            <td class="py-3 px-5 border-b border-gray-200">
                                                <p class="block antialiased font-sans text-xs font-medium text-gray-900 font-bold"
                                                    x-text="driver.akun.username"></p>
                                            </td>
                                            <td class="py-3 px-5 border-b border-gray-200 flex justify-end gap-2">
                                                <button
                                                    class="bg-orange-400 text-white rounded px-4 py-1 font-sans center"
                                                    @click="editData(driver)">Edit</button>
                                                <button class="bg-red-500 text-white rounded px-4 py-1 font-sans center"
                                                    @click="hapusData(driver)">Hapus</button>
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
                        <div
                            class="relative bg-clip-border rounded-xl overflow-hidden bg-transparent text-gray-700 shadow-none m-0">
                            <h6
                                class="block antialiased tracking-normal font-sans text-base font-semibold leading-relaxed text-gray-900 mb-2">
                                <span x-text="properties.sites.button_title"></span> Akun Driver
                            </h6>
                        </div>
                        <div class="w-full min-w-[200px] mb-4">
                            <label for="" class="font-sans text-base text-gray-500 mb-2 block">Nama Lengkap</label>
                            <input type="text" x-model="properties.form.nama"
                                class="w-full bg-transparent text-gray-700 font-sans font-normal outline outline-0 border-2 text-sm px-3 py-3 rounded-md border-gray-200 focus:border-gray-400"
                                autofocus>
                        </div>
                        <div class="w-full min-w-[200px] mb-4">
                            <label for="" class="font-sans text-base text-gray-500 mb-2 block">Kontak</label>
                            <input type="text" x-model="properties.form.kontak"
                                class="w-full bg-transparent text-gray-700 font-sans font-normal outline outline-0 border-2 text-sm px-3 py-3 rounded-md border-gray-200 focus:border-gray-400">
                        </div>
                        <div class="w-full min-w-[200px] mb-4">
                            <label for="" class="font-sans text-base text-gray-500 mb-2 block">Username</label>
                            <input type="text" x-model="properties.form.username"
                                class="w-full bg-transparent text-gray-700 font-sans font-normal outline outline-0 border-2 text-sm px-3 py-3 rounded-md border-gray-200 focus:border-gray-400"
                                :readonly="properties.sites.show_password_input == false">
                        </div>
                        <div class="w-full min-w-[200px] mb-4" x-show="properties.sites.show_password_input">
                            <label for="" class="font-sans text-base text-gray-500 mb-2 block">Password</label>
                            <input type="password" x-model="properties.form.password"
                                class="w-full bg-transparent text-gray-700 font-sans font-normal outline outline-0 border-2 text-sm px-3 py-3 rounded-md border-gray-200 focus:border-gray-400">
                        </div>
                        <div class="w-full min-w-[200px]">
                            <button @click="simpanData" type="submit"
                                class="bg-green-500 w-full text-white rounded py-4 font-sans center"
                                x-text="properties.sites.button_title"></button>
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
            "editData": function (driver) {
                this.properties.form.nama = driver.nama;
                this.properties.form.kontak = driver.kontak;
                this.properties.form.id = driver.id;
                this.properties.form.akun = driver.akun.id;
                this.properties.form.username = driver.akun.username;
                this.properties.form.password = '';

                this.properties.sites.show_password_input = false;

                this.properties.sites.query_title = `(dipilih: ${driver.nama})`;
                this.properties.sites.button_title = 'Perbaharui';
            },
            "simpanData": function () {
                this.clearMassage();

                let alpineObj = this;
                let elem = this.$event.target;
                let elemTitle = this.properties.sites.button_title;
                this.buttonLoading(elem);

                this.postData(
                    '/api/admin/driver/simpan',
                    this.createFormData({
                        'id': this.properties.form.id,
                        'nama': this.properties.form.nama,
                        'kontak': this.properties.form.kontak,
                        'akun': this.properties.form.akun,
                        'username': this.properties.form.username,
                        'password': this.properties.form.password
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
                            alpineObj.properties.data.list_driver.push(response.data.data);

                            alpineObj.properties.form.nama = "";
                            alpineObj.properties.form.kontak = "";
                            alpineObj.properties.form.id = -1;
                            alpineObj.properties.form.akun = -1;
                            alpineObj.properties.form.username = "";
                            alpineObj.properties.form.password = "";

                            alpineObj.addNormalMessage('form_response', `Berhasil! Driver ${response.data.data.nama} - ${response.data.data.kontak} telah disimpan.`);

                            return;
                        }

                        let index = alpineObj.properties.data.list_driver.findIndex(item => item.id == response.data.data.id);
                        alpineObj.properties.data.list_driver[index].nama = response.data.data.nama;
                        alpineObj.properties.data.list_driver[index].kontak = response.data.data.kontak;

                        alpineObj.addNormalMessage('form_response', `Berhasil! Driver ${response.data.data.nama} / ${response.data.data.kontak} telah diperbaharui.`);
                    },
                    function (err) {
                        alpineObj.addErrorMassage('bad_request', 'Gagal dalam menyimpan data, mohon periksa asal dan tujaun, lalu coba lagi.')
                    }
                )
            },
            "hapusData": function (driver) {
                if (!confirm('Anda yakin ingin menghapus driver ?')) return;

                this.clearMassage();

                let alpineObj = this;
                this.postData(
                    '/api/admin/driver/hapus',
                    this.createFormData({
                        'id': driver.id,
                    }),
                    function (response) {
                        let index = alpineObj.properties.data.list_driver.findIndex(item => item.id == response.data.data.deleted_id);
                        alpineObj.properties.data.list_driver.splice(index, 1);

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

                return date.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
            },
            "buttonLoading": function (elem, statusText = 'Mohon Tunggu') {
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
            "clearMassage": function () {
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
                        "list_driver": JSON.parse('<?= json_encode(array_map(fn($item) => $item->toArray(), $listDriver)) ?>')
                    },
                    "form": {
                        'id': -1,
                        'nama': '',
                        'kontak': '',
                        'akun': -1,
                        'username': '',
                        'password': ''
                    }
                },
                "init": function () { }
            })
        );
    });
</script>