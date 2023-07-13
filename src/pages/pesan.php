<?php

if (
    false === session()->isAuthenticatedAs('pelanggan')
) {
    response()->redirectTo(site_url('akun/login'), ['message' => 'Anda harus login terlebih dahulu.', 'status' => false]);
    exit();
}

$manager = app()->getManager();
$listDaerahOperasional = $manager->getService('DaerahOperasionalService')->listDaerahOperasional();
$listKategoriPenumpang = app()->getManager()->getService('KategoriPelangganService')->listKategoriPelanggan();
$selectedRute = $manager->getService('TiketService')->detailTiket($_GET['tiket'] ?? -1)?->toArray();

html_require_component('navbar');

?>
<main x-data="container">
    <div class="max-w-screen-xl mx-auto py-20 px-6">
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
        <form action="<?= site_url('pesan/pemesan') ?>" method="post">
            <div class="mb-4">
                <h2 class="block antialiased tracking-normal font-sans text-xl font-bold leading-relaxed text-gray-900">Pesan Tiket Keberangkatan</h2>
            </div>
            <div class="grid grid-cols-10 justify-stretch gap-4 w-full">
                <div class="col-span-6">
                    <div class="bg-gray-100 rounded p-6">
                        <div class="w-full mb-4">
                            <label for="" class="font-sans text-base text-gray-600 mb-2 block">Tanggal Keberangkatan: <small class="text-gray-500">(sekarang: <?= tanggal(date_create()) ?>)</small></label>
                            <input x-model="properties.form.tanggal_keberangkatan" type="date" class="w-full bg-white text-gray-700 font-sans font-normal outline outline-0 border-2 text-sm px-3 py-3 rounded-md border-gray-200 focus:border-gray-400">
                        </div>
                        <div class="w-full mb-4">
                            <label for="" class="font-sans text-base text-gray-600 mb-2 block">Keberangkatan (Asal - Tujuan)</label>
                            <div class="w-full mb-4 grid grid-cols-3 gap-4">
                                <select x-model="properties.form.asal" class="cursor-pointer w-full bg-white text-gray-700 font-sans font-normal outline outline-0 border-2 text-sm px-3 py-3 rounded-md border-gray-200 focus:border-gray-400">
                                    <option value="-1">-- Pilih Asal --</option>
                                    <template x-for="daerah in properties.data.list_daerah_operasional">
                                        <option :value="daerah.id" x-text="daerah.nama_kota" :selected="daerah.id == properties.data.selected_rute?.rute.asal.id"></option>
                                    </template>
                                </select>
                                <select x-model="properties.form.tujuan" class="cursor-pointer w-full bg-white text-gray-700 font-sans font-normal outline outline-0 border-2 text-sm px-3 py-3 rounded-md border-gray-200 focus:border-gray-400">
                                    <option value="-1">-- Pilih Tujuan --</option>
                                    <template x-for="daerah in properties.data.list_daerah_operasional">
                                        <option :value="daerah.id" x-text="daerah.nama_kota" :selected="daerah.id == properties.data.selected_rute?.rute.tujuan.id"></option>
                                    </template>
                                </select>
                                <select x-model="properties.form.kategori" class="cursor-pointer w-full bg-white text-gray-700 font-sans font-normal outline outline-0 border-2 text-sm px-3 py-3 rounded-md border-gray-200 focus:border-gray-400">
                                    <option value="-1">-- Pilih Kategori Penumpang --</option>
                                    <template x-for="tipe in properties.data.list_kategori_penumpang">
                                        <option :value="tipe.id" x-text="tipe.kategori" :selected="tipe.id == properties.data.selected_rute?.kategori.id"></option>
                                    </template>
                                </select>
                            </div>
                        </div>
                        <div class="w-full mt-8">
                            <button @click.prevent="cekTiket" type="button" class="w-full text-white bg-gray-700 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-4 py-4 text-center mr-3 md:mr-0">Cek Tiket dan Ketersedian Kursi</button>
                        </div>
                    </div>
                </div>
                <div class="col-span-4">
                    <div class="border border-gray-200 radius p-4">
                        <table class="w-full text-gray-600">
                            <tr>
                                <td class="w-48 pb-4">Tanggal Keberangkatan</td>
                                <td class="pb-4">: <span x-text="tanggalToIndo(properties.form.tanggal_keberangkatan)"></span></td>
                            </tr>
                            <tr>
                                <td class="w-48 pb-4">Rute Keberangkatan</td>
                                <td class="pb-4">: <span x-text="properties.sites.rute"></span></td>
                            </tr>
                        </table>
                        <div x-show="properties.sites.advance_form" style="display: none" class="transition-all">
                            <table class="w-full border-gray-200 border-t mb-2">
                                <tr>
                                    <td class="w-48 py-4">Jam Keberangkatan</td>
                                    <td class="py-4">
                                        <div class="flex justify-between">
                                            <span>: </span>
                                            <select x-model="properties.form.jam_keberangkatan" class="cursor-pointer w-full text-gray-700 font-sans font-normal outline outline-0">
                                                <option value="-1" x-text="properties.sites.jam_keberangkatan.length < 1 ? 'Ditentukan segera.' : '-- Pilih Jam --'"></option>
                                                <template x-for="jam in properties.sites.jam_keberangkatan">
                                                    <option :value="jam.id" x-text="jam.jam + ' WIB ( ' + jam.alias + ' )'"></option>
                                                </template>
                                            </select>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="w-48 pb-2">Mobil Tersedia</td>
                                    <td class="pb-2">
                                        <div class="flex justify-between items-center">
                                            <span>:</span>
                                            <select x-model="properties.form.mobil" @change="parseListKursi" class="cursor-pointer w-full text-gray-700 font-sans font-normal outline outline-0">
                                                <option value="-1" x-text="properties.sites.list_mobil.length < 1 ? 'Diatur di hari keberangkatan.' : '-- Pilih Mobil --'"></option>
                                                <template x-for="mobil in properties.sites.list_mobil">
                                                    <option :value="mobil.mobil.id" x-text="mobil.mobil.merk + ' - ' + mobil.mobil.plat_nomor"></option>
                                                </template>
                                            </select>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            <div>
                                <div class="mb-2">
                                    Silahkan Pilih Kursi:
                                </div>
                                <div class="flex flex-row-reverse flex-wrap w-full">
                                    <div class="w-2/3 py-8 text-center border border-gray-500 bg-gray-300 cursor-not-allowed">
                                        <span>Supir</span>
                                    </div>
                                    <template x-for="kursi in properties.sites.selected_mobil.details?.list_kursi">
                                        <div class="w-1/3 py-8 text-center border kursi-trigger hover:text-white"
                                             :class="kursi.tersedia ? 'border-gray-400 hover:bg-blue-500 bg-white cursor-pointer ' : 'bg-red-600 hover:bg-red-600 text-white cursor-not-allowed'"
                                             @click="addNomorKursi(kursi)" x-text="kursi.nomor">
                                        </div>
                                    </template>
                                </div>
                                <div class="w-full mt-2 text-sm text-right italic">
                                    <span class="text-red-400">Tidak tersedia.</span> - <span class="text-blue-400">Dipilih.</span>
                                </div>
                            </div>
                            <div class="w-full mt-8">
                                <button @click.prevent="pesanSekarang" class="w-full text-white bg-green-500 hover:bg-green-600 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-4 py-4 text-center mr-3 md:mr-0">Pesan Sekarang</button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <script type="text/javascript">
                document.addEventListener('alpine:init', () => {
                    const actions = {
                        "cekTiket": function () {
                            this.clearMassage();
                            this.clearListKursiDipilih();
                            let alpineObj = this;

                            this.getApiRequest('/api/pesan/cek-tiket', {
                                'tanggal_keberangkatan': this.properties.form.tanggal_keberangkatan,
                                'asal': this.properties.form.asal,
                                'tujuan': this.properties.form.tujuan,
                                'kategori': this.properties.form.kategori
                            }, function (response) {
                                alpineObj.parseRutePerjalanan(response.data.data.rute);
                                alpineObj.parseJamKeberangkatan(response.data.data.list_jam_keberangkatan_tersedia);
                                alpineObj.parseListMobil(response.data.data.list_mobil_tersedia);
                                alpineObj.properties.sites.advance_form = true;
                            }, function (error) {
                                console.error(error);
                                alpineObj.addErrorMassage('bad_request', error.response.data.errors.message);
                            });
                        },
                        "parseRutePerjalanan": function (rute) {
                            if (rute?.reversed) this.properties.sites.rute = `${rute.rute.tujuan.nama_kota} - ${rute.rute.asal.nama_kota}`;
                            else this.properties.sites.rute = `${rute.rute.asal.nama_kota} - ${rute.rute.tujuan.nama_kota}`
                        },
                        "parseJamKeberangkatan": function (jamKeberangkatan) {
                            this.properties.sites.jam_keberangkatan = jamKeberangkatan;
                        },
                        "parseListMobil": function(mobil) {
                            this.properties.sites.list_mobil = mobil;
                            if (mobil.length > 0) {
                                this.properties.sites.selected_mobil.details = mobil[0];
                                this.properties.sites.selected_mobil.total_kursi_penumpang = mobil[0].total_kursi_penumpang;
                            }
                        },
                        "parseListKursi": function () {
                            let indexSelected = this.properties.sites.list_mobil.findIndex(item => item.mobil.id == this.properties.form.mobil);
                            this.properties.sites.selected_mobil.details = this.properties.sites.list_mobil[indexSelected];
                            this.properties.sites.selected_mobil.total_kursi_penumpang = this.properties.sites.list_mobil[indexSelected].total_kursi_penumpang;

                            this.clearListKursiDipilih();
                        },
                        "addNomorKursi": function (kursi) {
                            if (kursi.tersedia === false) return;

                            let elem = this.$event.target;
                            let index = this.properties.form.list_nomor_kursi.indexOf(kursi.nomor);
                            if (index >= 0) {
                                elem.classList.remove('bg-blue-500');
                                elem.classList.remove('text-white');
                                this.properties.form.list_nomor_kursi.splice(index, 1);

                                return;
                            }

                            elem.classList.add('bg-blue-500');
                            elem.classList.add('text-white');
                            this.properties.form.list_nomor_kursi.push(kursi.nomor);

                            return;
                        },
                        "pesanSekarang": function () {
                            this.clearMassage();

                            let alpineObj = this;
                            let elem = this.$event.target;
                            let elemTitle = this.properties.sites.button_title;
                            this.buttonLoading(elem);

                            this.postData(
                                '/api/pesanan/create',
                                this.createFormData({
                                    'tanggal_keberangkatan': this.properties.form.tanggal_keberangkatan,
                                    'asal': this.properties.form.asal,
                                    'tujuan': this.properties.form.tujuan,
                                    'mobil': this.properties.form.mobil,
                                    'kategori': this.properties.form.kategori,
                                    'jam_keberangkatan': this.properties.form.jam_keberangkatan,
                                    'list_nomor_kursi': this.properties.form.list_nomor_kursi,
                                }),
                                function (response) {
                                    elem.disabled = false;
                                    elem.innerText = elemTitle;
                                    elem.classList.remove('bg-gray-700');
                                    elem.classList.remove('hover:bg-gray-700');
                                    elem.classList.remove('focus:ring-gray-700');
                                    elem.classList.remove('opacity-80');
                                    elem.classList.remove('cursor-not-allowed');

                                    window.location.href = `${alpineObj.properties.sites.api_url}/info-pemesan`;
                                },
                                function (error) {
                                    console.error(error);
                                    alpineObj.addErrorMassage('bad_request', error.response.data.errors.message);
                                }
                            );
                        },
                        "clearListKursiDipilih": function () {
                            this.properties.form.list_nomor_kursi = [];
                            let listKursiTrigger = document.getElementsByClassName('kursi-trigger');
                            for (const listKursiTriggerElement of listKursiTrigger) {
                                listKursiTriggerElement.classList.remove('bg-blue-500');
                                listKursiTriggerElement.classList.remove('text-white');
                            }
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
                        "getApiRequest": function (to, params = null, callback, errCallback) {
                            return axios
                                .get(this.properties.sites.api_url + to, { params: params })
                                .then(res => callback(res))
                                .catch(err => errCallback(err));
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

                            if (exists !== -1) {
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
                                    "rute": "",
                                    "jam_keberangkatan": [],
                                    "list_mobil": [],
                                    "selected_mobil": {
                                        'total_kursi_penumpang' : 7,
                                        'details': {
                                            'list_kursi' : [
                                                { 'nomor': 1, 'tersedia': true },
                                                { 'nomor': 2, 'tersedia': true },
                                                { 'nomor': 3, 'tersedia': true },
                                                { 'nomor': 4, 'tersedia': true },
                                                { 'nomor': 5, 'tersedia': true },
                                                { 'nomor': 6, 'tersedia': true },
                                                { 'nomor': 7, 'tersedia': true },
                                            ]
                                        }
                                    },
                                    "advance_form": false,
                                    "button_title": 'Pesan Sekarang'
                                },
                                "messages": {
                                    "errors": [],
                                    "normal": []
                                },
                                "data": {
                                    "selected_rute": JSON.parse('<?= json_encode($selectedRute) ?>'),
                                    "list_kategori_penumpang": JSON.parse('<?= json_encode(array_map(fn ($item) => $item->toArray(), $listKategoriPenumpang)) ?>'),
                                    "list_daerah_operasional": JSON.parse('<?= json_encode(array_map(fn ($item) => $item->toArray(), $listDaerahOperasional)) ?>')
                                },
                                "form": {
                                    "tanggal_keberangkatan": null,
                                    "asal": -1,
                                    "tujuan": -1,
                                    "kategori": -1,
                                    "jam_keberangkatan": -1,
                                    "mobil": -1,
                                    "list_nomor_kursi": []
                                }
                            },
                            "init": function () {
                                this.properties.form.tanggal_keberangkatan = "<?= date('Y-m-d') ?>";
                                if (this.properties.data.selected_rute) {
                                    this.properties.form.asal = this.properties.data.selected_rute.rute.asal.id;
                                    this.properties.form.tujuan = this.properties.data.selected_rute.rute.tujuan.id;
                                    this.properties.form.kategori = this.properties.data.selected_rute.kategori.id;
                                }
                            }
                        })
                    );
                });

            </script>
        </form>
    </div>
</main>
