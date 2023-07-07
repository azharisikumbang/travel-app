<?php html_require_component('navbar'); ?>
<main x-data="container">
    <div class="max-w-screen-xl mx-auto py-20 px-6">
        <?php if(session('temp')):
            html_alert(session('temp')['message'], 'yellow');
        endif; ?>
        <div x-if="Object.keys(properties.errors) > 0">
            <template x-for="key in Object.keys(properties.errors)">
                <?php html_alert("properties.errors[key].message", "properties.errors[key].status"); ?>
            </template>
        </div>
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
                            <label for="" class="font-sans text-base text-gray-600 mb-2 block">Keberangkatan</label>
                            <div class="w-full mb-4 grid grid-cols-3 gap-4">
                                <select x-model="properties.form.asal" name="jam_keberangkatan" class="w-full bg-white text-gray-700 font-sans font-normal outline outline-0 border-2 text-sm px-3 py-3 rounded-md border-gray-200 focus:border-gray-400">
                                    <option value="-1">-- Pilih Asal --</option>
                                    <template x-for="daerah in properties.data.daerah_operasional">
                                        <option :value="daerah.id" x-text="daerah.nama_kota" :selected="daerah.id == properties.data.tarif.kota_asal.id"></option>
                                    </template>
                                </select>
                                <select x-model="properties.form.tujuan" class="w-full bg-white text-gray-700 font-sans font-normal outline outline-0 border-2 text-sm px-3 py-3 rounded-md border-gray-200 focus:border-gray-400">
                                    <option value="-1">-- Pilih Tujuan --</option>
                                    <template x-for="daerah in properties.data.daerah_operasional">
                                        <option :value="daerah.id" x-text="daerah.nama_kota" :selected="daerah.id == properties.data.tarif.kota_tujuan.id"></option>
                                    </template>
                                </select>
                                <select x-model="properties.form.tipe_penumpang" class="w-full bg-white text-gray-700 font-sans font-normal outline outline-0 border-2 text-sm px-3 py-3 rounded-md border-gray-200 focus:border-gray-400">
                                    <option value="-1">-- Pilih Kategori Penumpang --</option>
                                    <template x-for="tipe in properties.data.tipe_penumpang">
                                        <option :value="tipe.id" x-text="tipe.tipe_penumpang" :selected="tipe.id == properties.data.tarif.tipe_penumpang.id"></option>
                                    </template>
                                </select>
                            </div>
                        </div>
                        <div class="w-full mt-8">
                            <button @click.prevent="cekTiketDanKursi" type="button" class="w-full text-white bg-gray-700 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-4 py-4 text-center mr-3 md:mr-0">Cek Tiket dan Ketersedian Kursi</button>
                        </div>
                    </div>
                </div>
                <div class="col-span-4">
                </div>
            </div>
        </form>
    </div>
</main>
<script type="text/javascript">
    document.addEventListener('alpine:init', () => {
        const actions = {
            "loadTarifById": function (id) {
                return this.getApiRequest('/api/tarif', { tarif: id });
            },
            "loadDaerahOperasional": function () {
                return this.getApiRequest('/api/list-daerah-operasional');
            },
            "loadTipePenumpang": function () {
                return this.getApiRequest('/api/list-tipe-penumpang');
            },
            "cekTiketDanKursi": function () {
                this.postData('')
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
            "postData": function (to, data, callback) {
                return axios
                    .get(this.properties.sites.api_url + to, { params: params })
                    .then(res => callback(res))
                    .catch(err => console.log(err));
            }
        };

        Alpine.data('container',
            () => ({
                ...actions,
                ...utils,
                "properties": {
                    "sites": {
                        "api_url": "<?= site_url() ?>",
                        "show_form": true,
                    },
                    "errors": {},
                    "data": {
                        "daerah_operasional": [],
                        "tipe_penumpang": [],
                        "list_jam_keberangkatan": [],
                        "list_mobil": [],
                        "nomor_tarif": <?= $_GET['tarif'] ?>,
                        "tarif": {}
                    },
                    "form": {
                        "tarif": null,
                        "asal": null,
                        "tujuan": null,
                        "tipe_penumpang": null,
                        "tanggal_keberangkatan": null,
                        "jam_keberangkatan": null,
                        "mobil": null,
                        "list_nomor_kursi": []
                    }
                },
                "init": async function() {
                    this.properties.data.tarif = (await this.loadTarifById(this.properties.data.nomor_tarif)).data;

                    this.properties.form.tarif = this.properties.data.tarif.id;
                    this.properties.form.asal = this.properties.data.tarif.kota_asal.id;
                    this.properties.form.tujuan = this.properties.data.tarif.kota_tujuan.id;

                    this.properties.data.daerah_operasional = (await this.loadDaerahOperasional()).data;

                    this.properties.data.tipe_penumpang = (await this.loadTipePenumpang()).data;

                }
            })
        );
    });

</script>
