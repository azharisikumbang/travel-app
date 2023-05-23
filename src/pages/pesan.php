<?php html_require_component('navbar'); ?>
<main x-data="container">
    <div class="max-w-screen-xl mx-auto py-20 px-6">
        <?php if(session('temp')):
            html_alert(session('temp')['message'], 'yellow');
        endif; ?>
        <div x-if="errors.length > 1">
            <template x-for="error in errors">
                <?php html_alert("error.message", "red");  ?>
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
                                <input x-model="selected.tanggal" type="date" class="w-full bg-transparent text-gray-700 font-sans font-normal outline outline-0 border-2 text-sm px-3 py-3 rounded-md border-gray-200 focus:border-gray-400">
                            </div>
                            <div>
                                <label for="" class="font-sans text-base text-gray-600 mb-2 block">Jam Keberangkatan</label>
                                <select x-model="selected.jam" name="jam_keberangkatan" class="w-full bg-transparent text-gray-700 font-sans font-normal outline outline-0 border-2 text-sm px-3 py-3 rounded-md border-gray-200 focus:border-gray-400">
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
                                <select x-model="selected.asal" name="jam_keberangkatan" class="w-full bg-transparent text-gray-700 font-sans font-normal outline outline-0 border-2 text-sm px-3 py-3 rounded-md border-gray-200 focus:border-gray-400">
                                    <option value="_">-- Pilih Asal --</option>
                                    <template x-for="daerah in data.listDaerahOperasional">
                                        <option :value="daerah.id" x-text="daerah.nama_kota" :selected="daerah.id == data.tarif.kota_asal.id"></option>
                                    </template>
                                </select>
                                <select x-model="selected.tujuan" class="w-full bg-transparent text-gray-700 font-sans font-normal outline outline-0 border-2 text-sm px-3 py-3 rounded-md border-gray-200 focus:border-gray-400">
                                    <option value="_">-- Pilih Tujuan --</option>
                                    <template x-for="daerah in data.listDaerahOperasional">
                                        <option :value="daerah.id" x-text="daerah.nama_kota" :selected="daerah.id == data.tarif.kota_tujuan.id"></option>
                                    </template>
                                </select>
                                <select x-model="selected.tipePenumpang" class="w-full bg-transparent text-gray-700 font-sans font-normal outline outline-0 border-2 text-sm px-3 py-3 rounded-md border-gray-200 focus:border-gray-400">
                                    <option value="_">-- Pilih Kategori Penumpang --</option>
                                    <template x-for="tipe in data.listTipePenumpang">
                                        <option :value="tipe.id" x-text="tipe.tipe_penumpang" :selected="tipe.id == data.tarif.tipe_penumpang.id"></option>
                                    </template>
                                </select>
                            </div>
                        </div>
                        <div class="w-full mt-8">
                            <button type="button" @click="cekKetersediaanKursi" class="w-full text-white bg-gray-700 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-4 py-4 text-center mr-3 md:mr-0">Cek Mobil dan Kursi</button>
                        </div>
                        <template x-if="data.listKursiTersedia.jumlah_kursi_tersedia > 0">
                            <div class="mt-8">
                                <div class="mb-2">
                                    Mobil : <span x-text="data.mobil.merk"></span> ( <span x-text="data.mobil.plat_nomor"></span> )
                                </div>
                                <div class="grid grid-cols-3 w-full gap-2">
                                    <template x-for="kursi in data.listKursiTersedia.list_kursi">
                                        <div class="py-8 text-center border-2 rounded"
                                            :class="kursi.tersedia ? 'border-gray-400 hover:bg-gray-300 bg-white cursor-pointer ' : 'border-red-800 bg-red-800 hover:bg-red-800 text-white cursor-not-allowed'"
                                            x-bind:style="kursi.dipilih && { borderColor: 'rgb(59 130 246)', backgroundColor: 'rgb(59 130 246)', color: 'white' }"
                                            @click="addNomorKursi(kursi.nomor)">
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
                                <td>: <span x-text="parseTanggalToIndo(selected.tanggal)"></span> <span x-text="parseJamKeberangkatan"></span></td>
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
                errors: [],
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
                    kursi: []
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
                        .then(response => this.data.listJamKeberangkatan = response.data);

                    axios.get(this.apiUrl + '/api/list-tipe-penumpang')
                        .then(response => this.data.listTipePenumpang = response.data);

                    axios.get(this.apiUrl + '/api/list-daerah-operasional')
                        .then(response => this.data.listDaerahOperasional = response.data);
                },
                pesanSekarang() {
                    axios.post(this.apiUrl + '/api/create-pesanan')
                        .then(res => console.log(res))
                },
                addError(message, status = 'yellow') {
                    this.errors.push({ message: message, status: status });
                },
                cekKetersediaanKursi() {
                    if (this.selected.tanggal == "") this.addError('Tanggal keberangkatan belum diisi, mohon isi terlebih dahulu');
                    if (this.selected.jam == "") this.addError('Jam keberangkatan belum diisi, mohon isi terlebih dahulu');
                    if (this.selected.asal == "") this.addError('Asal keberangkatan belum diisi, mohon isi terlebih dahulu');
                    if (this.selected.tujuan == "") this.addError('Tujuan keberangkatan belum diisi, mohon isi terlebih dahulu');
                    if (this.selected.tipePenumpang == "") this.addError('Kategori penumpang belum diisi, mohon isi terlebih dahulu');

                    // if(this.errors.length > 0) return;

                    // call api
                    let listKursiTersedia = {
                        id_mobil: 1,
                        jumlah_kursi_penumpang: 8,
                        jumlah_kursi_tersedia: 8,
                        tanggal: new Date().toDateString(),
                        asal: {
                            id: 1,
                            nama_kota: 'Pelalawan',
                        },
                        tujuan: {
                            id: 2,
                            nama_kota: 'Padang'
                        },
                        tipe_penumpang: {
                            id: 1,
                            tipe_penumpang: 'Umum'
                        },
                        list_kursi: [
                            { nomor: 1, tersedia: true, dipilih: false },
                            { nomor: 2, tersedia: true, dipilih: false },
                            { nomor: 3, tersedia: true, dipilih: false },
                            { nomor: 4, tersedia: true, dipilih: false },
                            { nomor: 5, tersedia: true, dipilih: false },
                            { nomor: 6, tersedia: false, dipilih: false },
                            { nomor: 7, tersedia: true, dipilih: false },
                            { nomor: 8, tersedia: false, dipilih: false },
                        ]
                    };

                    this.data.listKursiTersedia = listKursiTersedia;
                    this.selected.kursi = [];

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

                    return listDaerah[indexAsal].nama_kota + ' - ' + listDaerah[indexTujuan].nama_kota
                },
                parseTipePenumpang() {
                    return this.data.listTipePenumpang[
                        this.data.listTipePenumpang.findIndex(el => el.id == this.selected.tipePenumpang)
                        ].tipe_penumpang;
                },
                toRupiah(number) {
                    return new Intl.NumberFormat('id-Id', { maximumSignificantDigits: 3 }).format(number);
                },
                addNomorKursi(nomor) {
                    let indexKursi = this.data.listKursiTersedia.list_kursi.findIndex(el => el.nomor == nomor);
                    let kursi = this.data.listKursiTersedia.list_kursi[indexKursi];

                    if(false == kursi.tersedia) return;

                    this.data.listKursiTersedia.list_kursi[indexKursi].dipilih = !this.data.listKursiTersedia.list_kursi[indexKursi].dipilih;

                    let indexAddedArray = this.selected.kursi.indexOf(kursi.nomor);
                    if(indexAddedArray == -1) {
                        this.selected.kursi.push(kursi.nomor);
                        return;
                    }

                    this.selected.kursi.splice(indexAddedArray, 1);
                }
            });
        });
    });

</script>
