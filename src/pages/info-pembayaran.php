<?php html_require_component('navbar'); ?>
<main x-data="container">
    <div class="max-w-screen-xl mx-auto py-20 px-6">
        <?php if(session('temp')):
            html_alert(session('temp')['message'], 'yellow');
        endif; ?>
        <div x-if="Object.keys(properties.errors) > 0">
            <template x-for="key in Object.keys(properties.errors)">
                <?php html_alert("properties.errors[key]?.message", "properties.errors[key]?.status"); ?>
            </template>
        </div>
        <form @submit.prevent="submit">
            <div class="mb-4">
                <h2 class="block antialiased tracking-normal font-sans text-xl font-bold leading-relaxed text-gray-900">Informasi Pembayaran</h2>
            </div>
            <div x-show="properties.data.sesi_pesanan">
                <div class="mb-4 px-4 py-6 text-green-600 border-green-600 border rounded">
                    <!-- @TODO: ganti nama dan bank pembayaran -->
                    <p><span class="font-bold">Perhatian!</span> Pesanan anda telah dicatat, silahkan lakukan pembayaran ke <strong>BANK BRI 2116 0101 0052 505</strong> atas nama <strong>FEBRY SELVY ANDRI</strong> paling lambat 2 jam setelah pemesanan.</p>
                    <p>Minimum pembayaran berupa DP adalah 50 % dari total tagihan.</p>
                    <p>Anda dapat mengakses kembali detail pesanan di <a href="" class="underline">portal pelanggan</a> untuk melakukan pembayaran di lain waktu.</p>
                </div>
            </div>
            <div class="grid grid-cols-10 justify-stretch gap-4 w-full">
                <div class="col-span-6">
                    <div class="bg-gray-100 rounded p-6">
                        <div class="w-full mb-4">
                            <label for="" class="font-sans text-base text-gray-600 mb-2 block">Atas Nama Pembayaran</label>
                            <input x-model="properties.form.nama" type="text" class="w-full bg-white text-gray-700 font-sans font-normal outline outline-0 border-2 text-sm px-3 py-3 rounded-md border-gray-200 focus:border-gray-400">
                        </div>
                        <div class="w-full mb-4">
                            <label for="" class="font-sans text-base text-gray-600 mb-2 block">Nama Bank Pembayaran</label>
                            <input x-model="properties.form.bank" type="text" class="w-full bg-white text-gray-700 font-sans font-normal outline outline-0 border-2 text-sm px-3 py-3 rounded-md border-gray-200 focus:border-gray-400">
                        </div>
                        <div class="w-full mb-4">
                            <label for="" class="font-sans text-base text-gray-600 mb-2 block">Nominal Dibayarkan <small>(minimal bayar: Rp <span x-text="toRupiah(properties.data.sesi_pesanan.total_tarif / 2)"></span>)</small></label>
                            <input x-model="properties.form.nominal" type="number" class="w-full bg-white text-gray-700 font-sans font-normal outline outline-0 border-2 text-sm px-3 py-3 rounded-md border-gray-200 focus:border-gray-400">
                        </div>
                        <div class="w-full mb-4">
                            <label for="" class="font-sans text-base text-gray-600 mb-2 block">Upload Bukti Pembayaran</label>
                            <input accept="image/*" @change="properties.form.bukti = Object.values($event.target.files)[0]" type="file" class="w-full text-gray-700 font-sans font-normal outline outline-0">
                        </div>
                        <div class="w-full mt-8 flex flex-row gap-4">
                            <a href="<?= site_url('pelanggan/pesanan/menunggu-pembayaran') ?>" class="w-full text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-4 py-4 text-center mr-3 md:mr-0">Bayar Nanti</a>
                            <button type="submit" class="w-full text-white bg-gray-700 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-4 py-4 text-center mr-3 md:mr-0">Simpan dan Lanjutkan</button>
                        </div>
                    </div>
                </div>
                <div class="col-span-4">
                    <div class="rounded-lg border">
                        <h6 class="text-center py-4 rounded-tl rounded-tr font-sans font-semibold text-gray-700 border-b">Rangkuman</h6>
                        <table class="w-full text-gray-600">
                            <tr>
                                <td class="px-4 py-5 pb-2 w-1/2 ">Nomor Pesanan</td>
                                <td>: <span class="font-bold underline" x-text="properties.data.sesi_pesanan.nomor_pemesanan"></span></td>
                            </tr>
                            <tr class="">
                                <td class="px-4 py-2">Status Pesanan</td>
                                <td>: <span class="italic" x-text="properties.data.sesi_pesanan.status_pemesanan"></span></td>
                            </tr>
                            <tr class="">
                                <td class="px-4 py-2">Jadwal Keberangkatan</td>
                                <td>: <span x-text="parseTanggalToIndo(properties.data.sesi_pesanan.tanggal_keberangkatan.date)"></span> <span x-text="properties.data.sesi_pesanan.jam_keberangkatan"></span> WIB</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 w-1/2">Rute Perjalanan</td>
                                <td>: <span x-text="properties.data.sesi_pesanan.kota_asal"></span> - <span x-text="properties.data.sesi_pesanan.kota_tujuan"></span></td>
                            </tr>
                            <tr class="">
                                <td class="px-4 py-2 w-1/2">Nomor Kursi</td>
                                <td>: <span x-text="displayNomorKursi"></span> <i>(<span x-text="properties.data.sesi_pesanan.list_kursi_dipesan.length"></span> kursi)</i></td>
                            </tr>
                            <tr>
                                <td class="px-4 pt-2 pb-4 w-1/2">Tarif Per Tiket</td>
                                <td>: Rp <span x-text="toRupiah(properties.data.sesi_pesanan.total_tarif / properties.data.sesi_pesanan.list_kursi_dipesan.length)"></span> (<span x-text="properties.data.sesi_pesanan.tipe_penumpang"></span>)</td>
                            </tr>
                            <tr class="font-bold border-t">
                                <td class="px-4 py-4 w-1/2">Total Tarif Tiket</td>
                                <td>: Rp <span x-text="toRupiah(properties.data.sesi_pesanan.total_tarif)"></span></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </form>
    </div>
</main>
<script type="text/javascript">
    document.addEventListener('alpine:init', () => {
        // @TODO: separate to file
        const actions = {
            "submit": function () {
                console.log(this.properties.errors);
                this.removeError('bad_request');
                this.isInputFilled('nama', 'nama', "Nama pemesan masih kosong, mohon diisi.");
                this.isInputFilled('bank', 'bank', "Nomor handphone pemesan masih kosong, mohon diisi.");
                this.isInputFilled('nominal', 'nominal', "Alamat titik jemput masih kosong, mohon diisi.");

                if(Object.keys(this.properties.errors).length < 1) {
                    const form = new FormData();
                    form.append('nama', this.properties.form.nama);
                    form.append('bank', this.properties.form.bank);
                    form.append('nominal', this.properties.form.nominal);
                    form.append('bukti', this.properties.form.bukti);

                    axios
                        .post(this.properties.sites.api_url + '/api/pesanan/info-pembayaran/simpan', form, {
                            "headers": {
                                "Content-Type": "multipart/form-data"
                            }
                        })
                        .then(response => window.location.href = this.properties.sites.api_url + '/pelanggan')
                        .catch(err => this.addError('bad_request', err.response.data.errors[0], 'red')); // @TODO: errors should not be access directed

                }
            },
            "displayNomorKursi": function() {
                return this.properties.data.sesi_pesanan.list_kursi_dipesan.map(item => item.nomor_kursi).join(', ');
            }
        };

        const utils = {
            "addError": function (key, message, status = 'yellow') {
                this.removeError(key);
                this.properties.errors[key] = {"message": message, "status": status}
            },
            "removeError": function (key) {
                if (this.properties.errors.hasOwnProperty(key)) delete this.properties.errors[key];
            },
            "isInputFilled": function (name, errorKey, errorMessage) {
                if (this.properties.form[name] === "") this.addError(errorKey, errorMessage);
                else this.removeError(errorKey);
            },
            "toRupiah": function (number) {
                return new Intl.NumberFormat('id-Id', {"maximumSignificantDigits": 3}).format(number);
            },
            "parseTanggalToIndo": function (tanggal) {
                if (!tanggal) return null;
                let date = new Date(tanggal);

                return date.toLocaleDateString('id-ID',  { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
            }
        };

        Alpine.data('container',
            () => ({
                ...actions,
                ...utils,
                "properties": {
                    "sites": {
                        "api_url": "<?= site_url() ?>",
                    },
                    "errors": {},
                    "data": <?= json_encode(['sesi_pesanan' => session('pesanan')]) ?>,
                    "form": {
                        "nama": "",
                        "bank": "",
                        "nominal": 0,
                        "bukti": ""
                    }
                },
                "init": function() {
                    this.properties.form.nominal = this.properties.data.sesi_pesanan.total_tarif
                }
            })
        );
    });

</script>
