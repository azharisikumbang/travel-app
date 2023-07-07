<?php html_require_component('navbar'); ?>
<main x-data="container">
    <div class="max-w-screen-xl mx-auto py-20 px-6">
        <?php if(session('temp')):
            html_alert(session('temp')['message'], 'yellow');
        endif; ?>
        <div x-if="Object.keys(errors) > 0">
            <template x-for="key in Object.keys(errors)">
                <?php html_alert("errors[key].message", "errors[key].status"); ?>
            </template>
        </div>
        <form action="<?= site_url('pesan/pemesan') ?>" method="post">
            <div class="mb-4">
                <h2 class="block antialiased tracking-normal font-sans text-xl font-bold leading-relaxed text-gray-900">Pesan Tiket Keberangkatan</h2>
            </div>
            <div class="grid grid-cols-10 justify-stretch gap-4 w-full">
                <div class="col-span-6">
                    <div class="bg-gray-100 rounded p-6">
                        <div class="w-full mb-4 grid grid-cols-3 gap-4">
                            <div class="col-span-2">
                                <label for="" class="font-sans text-base text-gray-600 mb-2 block">Tanggal Keberangkatan</label>
                                <input x-model="selected.tanggal" type="date" class="w-full bg-white text-gray-700 font-sans font-normal outline outline-0 border-2 text-sm px-3 py-3 rounded-md border-gray-200 focus:border-gray-400">
                            </div>
                            <div>
                                <label for="" class="font-sans text-base text-gray-600 mb-2 block">Jam Keberangkatan</label>
                                <select x-model="selected.jam" name="jam_keberangkatan" class="w-full bg-white text-gray-700 font-sans font-normal outline outline-0 border-2 text-sm px-3 py-3 rounded-md border-gray-200 focus:border-gray-400">
                                    <option value="_">-- Pilih Jam --</option>
                                    <template x-for="jam in data.listJamKeberangkatan">
                                        <option :value="jam.id" x-text="jam.jam + ' WIB / ' + jam.alias"></option>
                                    </template>
                                </select>
                            </div>
                        </div>
                        <div class="w-full mb-4">
                            <label for="" class="font-sans text-base text-gray-600 mb-2 block">Keberangkatan</label>
                            <div class="w-full mb-4 grid grid-cols-3 gap-4">
                                <select x-model="selected.asal" name="jam_keberangkatan" class="w-full bg-white text-gray-700 font-sans font-normal outline outline-0 border-2 text-sm px-3 py-3 rounded-md border-gray-200 focus:border-gray-400">
                                    <option value="_">-- Pilih Asal --</option>
                                    <template x-for="daerah in data.listDaerahOperasional">
                                        <option :value="daerah.id" x-text="daerah.nama_kota" :selected="daerah.id == data.tarif.kota_asal.id"></option>
                                    </template>
                                </select>
                                <select x-model="selected.tujuan" class="w-full bg-white text-gray-700 font-sans font-normal outline outline-0 border-2 text-sm px-3 py-3 rounded-md border-gray-200 focus:border-gray-400">
                                    <option value="_">-- Pilih Tujuan --</option>
                                    <template x-for="daerah in data.listDaerahOperasional">
                                        <option :value="daerah.id" x-text="daerah.nama_kota" :selected="daerah.id == data.tarif.kota_tujuan.id"></option>
                                    </template>
                                </select>
                                <select x-model="selected.tipePenumpang" class="w-full bg-white text-gray-700 font-sans font-normal outline outline-0 border-2 text-sm px-3 py-3 rounded-md border-gray-200 focus:border-gray-400">
                                    <option value="_">-- Pilih Kategori Penumpang --</option>
                                    <template x-for="tipe in data.listTipePenumpang">
                                        <option :value="tipe.id" x-text="tipe.tipe_penumpang" :selected="tipe.id == data.tarif.tipe_penumpang.id"></option>
                                    </template>
                                </select>
                            </div>
                        </div>
                        <div class="w-full mt-8">
                            <button type="button" @click="cekKetersediaanKursi" class="w-full text-white bg-gray-700 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-4 py-4 text-center mr-3 md:mr-0">Cek Tiket dan Ketersedian Kursi</button>
                        </div>
                        <template x-if="data.listKursiTersedia.list_kursi.total > 0">
                            <div class="mt-8">
                                <div class="mb-2">
                                    Silahkan Pilih Kursi Tersedia:
                                </div>
                                <div class="flex flex-row-reverse flex-wrap w-full">
                                    <div class="w-1/3 py-8 text-center border border-gray-500 bg-gray-400 cursor-not-allowed">
                                        <span>Supir</span>
                                    </div>
                                    <template x-for="(kursi, index) in data.listKursiTersedia.list_kursi.detail">
                                        <div class="w-1/3 py-8 text-center border"
                                             :class="kursi.tersedia ? 'border-gray-400 hover:bg-gray-300 bg-white cursor-pointer ' : 'border-red-800 bg-red-800 hover:bg-red-800 text-white cursor-not-allowed'"
                                             x-bind:style="kursi.dipilih && { borderColor: 'rgb(255 255 255)', backgroundColor: 'rgb(59 130 246)', color: 'white' }"
                                             @click="addNomorKursi(index, kursi.nomor)">
                                            <span x-text="kursi.nomor"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
                <div class="col-span-4">
                    <div class="rounded-lg border">
                        <h6 class="text-center py-4 text-gray-700 font-sans font-semibold">Rangkuman</h6>
                        <table class="w-full text-gray-600">
                            <tr class="bg-gray-100">
                                <td class="p-4 w-1/2">Jadwal Keberangkatan</td>
                                <td>: <span x-text.lazy="parseTanggalToIndo(selected.tanggal)"></span> <span x-text.lazy="parseJamKeberangkatan"></span></td>
                            </tr>
                            <tr>
                                <td class="p-4 w-1/2">Rute Perjalanan</td>
                                <td>: <span x-text="parsePerjalanan(selected.asal, selected.tujuan)"></span></td>
                            </tr>
                            <tr class="bg-gray-100">
                                <td class="p-4 w-1/2">Nomor Kursi</td>
                                <td>: <span x-text="selected.kursi.sort().join(', ')"></span> <i>(<span x-text="selected.kursi.length"></span> kursi)</i></td>
                            </tr>
                            <tr>
                                <td class="p-4 w-1/2">Tarif Per Tiket</td>
                                <td>: Rp <span x-text="toRupiah(selected.tarif)"></span> (<span x-text="parseTipePenumpang"></span>)</td>
                            </tr>
                            <tr class="bg-gray-200 font-bold">
                                <td class="p-4 w-1/2">Total Tarif Tiket</td>
                                <td>: Rp <span x-text="toRupiah(selected.tarif * selected.kursi.length)"></span></td>
                            </tr>
                        </table>
                    </div>
                    <div class="mt-4">
                        <button @click.prevent="pesanSekarang" class="w-full text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-4 py-4 text-center mr-3 md:mr-0">Pesan Sekarang</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</main>
<script type="text/javascript">
    document.addEventListener('alpine:init', () => {
        Alpine.data('container', () => {
            return ({
                apiUrl: "<?= site_url() ?>",
                currentTarif: <?= $_GET['tarif'] ?>,
                errors: {},
                data: {
                    listJamKeberangkatan: [],
                    listTipePenumpang: [],
                    listDaerahOperasional: [],
                    tarif: {},
                    mobil: { // manual
                        id: 1,
                        jumlah_kursi_penumpang: 8,
                        merk: 'Anggap se avanza',
                        plat_nomor: 'BA 1234 XI'
                    },
                    listKursiTersedia: []
                },
                selected: {
                    tanggal: "",
                    jam: "",
                    asal: "",
                    tujuan: "",
                    tipePenumpang: "",
                    mobil: 1,
                    kursi: [],
                    tarif: 0
                },
                init() {
                    axios.get(this.apiUrl + '/api/tarif', {
                        params: {
                            tarif: this.currentTarif
                        }
                    }).then(response => {
                        this.data.tarif = response.data;
                        this.selected.asal = this.data.tarif.kota_asal.id;
                        this.selected.tujuan = this.data.tarif.kota_tujuan.id;
                        this.selected.tipePenumpang = this.data.tarif.tipe_penumpang.id;
                        this.selected.tarif = this.data.tarif.tarif;
                    });

                    axios.get(this.apiUrl + '/api/list-jam-keberangkatan')
                        .then(response => this.data.listJamKeberangkatan = response.data.data);

                    axios.get(this.apiUrl + '/api/list-tipe-penumpang')
                        .then(response => this.data.listTipePenumpang = response.data.data);

                    axios.get(this.apiUrl + '/api/list-daerah-operasional')
                        .then(response => this.data.listDaerahOperasional = response.data.data);
                },
                pesanSekarang() {
                    this.removeError('bad_request');

                    let form = new FormData();
                    form.append('tanggal_keberangkatan', this.selected.tanggal);
                    form.append('jam_keberangkatan', this.selected.jam);
                    form.append('asal_keberangkatan', this.selected.asal);
                    form.append('tujuan_keberangkatan', this.selected.tujuan);
                    form.append('kategori_penumpang', this.selected.tipePenumpang);
                    form.append('mobil', this.selected.mobil);
                    for (const kursi of this.selected.kursi) {
                        form.append('kursi_dipesan[]', kursi);
                    }
                    // cek di sisi server
                    // form.append('tarif_satuan', this.selected.tarif);
                    // form.append('tarif_total', (this.selected.tarif * this.selected.kursi.length));

                    axios
                        .post(this.apiUrl + '/api/pesanan/create', form)
                        .then(res => window.location.href = this.apiUrl + '/pesan/info-pemesan')
                        .catch(err => this.addError('bad_request', err.response.data.errors[0], 'red'));
                },
                addError(key, message, status = 'yellow') {
                    this.removeError(key);
                    this.errors[key] = { message: message, status: status }
                },
                removeError(key) {
                    if (this.errors.hasOwnProperty(key)) delete this.errors[key];
                },
                isInputFilled(name, errorKey, errorMessage) {
                    if (this.selected[name] === "") this.addError(errorKey, errorMessage);
                    else this.removeError(errorKey);
                },
                async cekKetersediaanKursi() {
                    this.removeError('bad_request');
                    this.data.listKursiTersedia = {};
                    this.selected.kursi = [];

                    this.isInputFilled('tanggal', 'tanggal', 'Tanggal keberangkatan belum diisi, mohon isi terlebih dahulu');
                    this.isInputFilled('jam', 'jam', 'Jam keberangkatan belum diisi, mohon isi terlebih dahulu');
                    this.isInputFilled('asal', 'asal', 'Asal keberangkatan belum diisi, mohon isi terlebih dahulu');
                    this.isInputFilled('tujuan', 'tujuan', 'Tujuan keberangkatan belum diisi, mohon isi terlebih dahulu');
                    this.isInputFilled('tipePenumpang', 'tipePenumpang', 'Kategori penumpang belum diisi, mohon isi terlebih dahulu');
                    await this.hitungUlangTiket();

                    if(Object.keys(this.errors).length < 1) {

                        axios
                            .get(this.apiUrl + `/api/cek-tiket?asal=${this.selected.asal}&tujuan=${this.selected.tujuan}&kategori=${this.selected.tipePenumpang}&tanggal=${this.selected.tanggal}&jam=${this.selected.jam}`)
                            .then(response => {
                                this.data.listKursiTersedia = response.data.data;
                                this.selected.kursi = [];
                            })
                            .catch(err => this.addError('bad_request', err.response.data.errors[0], 'red'))
                        ;

                    }

                },
                parseTanggalToIndo(tanggal) {
                    if (!tanggal) return null;
                    let date = new Date(tanggal);

                    return date.toLocaleDateString('id-ID');
                },
                parseJamKeberangkatan() {
                    let result = this.data.listJamKeberangkatan.findIndex(el => el.id == this.selected.jam);

                    return this.data.listJamKeberangkatan[result].jam + ' WIB (' + this.data.listJamKeberangkatan[result].alias + ')';
                },
                parsePerjalanan() {
                    let listDaerah = this.data.listDaerahOperasional;
                    // asal
                    let indexAsal = listDaerah.findIndex(el => el.id == this.selected.asal);
                    let indexTujuan = listDaerah.findIndex(el => el.id == this.selected.tujuan);

                    return listDaerah[indexAsal].nama_kota + ' - ' + listDaerah[indexTujuan].nama_kota;
                },
                parseTipePenumpang() {
                    return this.data.listTipePenumpang[
                        this.data.listTipePenumpang.findIndex(el => el.id == this.selected.tipePenumpang)
                        ].tipe_penumpang;
                },
                toRupiah(number) {
                    return new Intl.NumberFormat('id-Id', { maximumSignificantDigits: 3 }).format(number);
                },
                addNomorKursi(index, nomor) {
                    let kursi = this.data.listKursiTersedia.list_kursi.detail[index];

                    if(false == kursi.tersedia || kursi.nomor != nomor) return;

                    if (false === kursi.hasOwnProperty('dipilih')) {
                        this.data.listKursiTersedia.list_kursi.detail[index]['dipilih'] = true;
                    } else {
                        this.data.listKursiTersedia.list_kursi.detail[index].dipilih = !this.data.listKursiTersedia.list_kursi.detail[index].dipilih;
                    }

                    let indexAddedArray = this.selected.kursi.indexOf(kursi.nomor);
                    if(indexAddedArray === -1) {
                        this.selected.kursi.push(kursi.nomor);
                        return;
                    }

                    this.selected.kursi.splice(indexAddedArray, 1);
                },
                async hitungUlangTiket() {
                    await this.removeError('rute');
                    await axios.get(this.apiUrl + `/api/cari-tarif?asal=${this.selected.asal}&tujuan=${this.selected.tujuan}&kategori=${this.selected.tipePenumpang}`)
                        .then(response => {
                            this.data.tarif = response.data.data;
                            this.selected.asal = this.data.tarif.kota_asal.id;
                            this.selected.tujuan = this.data.tarif.kota_tujuan.id;
                            this.selected.tipePenumpang = this.data.tarif.tipe_penumpang.id;
                            this.selected.tarif = this.data.tarif.tarif;
                        })
                        .catch(error => {
                            this.addError('rute', error.response.data.message);
                            this.selected.tarif = 0;
                        });
                }
            });
        });
    });

</script>
