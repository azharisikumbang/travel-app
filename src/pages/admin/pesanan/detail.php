<?php

/** @var $pesanan Pesanan */
$pesanan = app()->getManager()->getService('PemesananService')->cariPesananBerdasarkanNomorPesanan($_GET['nomor']);

if (is_null($pesanan)) html_not_found();

?>
<main x-data="container">
    <nav class="block w-full max-w-full bg-transparent text-white shadow-none transition-all px-0 py-1 border-b-2">
        <div class="flex flex-col-reverse justify-between gap-6 md:flex-row md:items-center">
            <h2 class="block antialiased tracking-normal font-sans text-2xl font-semibold leading-relaxed text-gray-900" x-text="properties.sites.page_title"></h2>
            <div class="flex items-center gap-4">
                <span class="font-sans text-gray-500">Sekarang: <?php echo tanggal(date_create()) ?></span>
                <a @click="window.location.reload()" class="underline text-gray-500 hover:text-gray-600 cursor-pointer">Muat Ulang</a>
            </div>
        </div>
    </nav>
    <div id="content" class="mt-8 w-full overflow-hidden grid grid-cols-2 gap-4">
        <div>
            <div class="rounded-lg border bg-white p-8">
                <h6 class="rounded-tl rounded-tr font-sans text-xl font-semibold text-gray-700 mb-2">Informasi Pemesanan (Tiket)</h6>
                <div class="w-full border-b py-2 mb-2">
                    <label class="text-gray-700 font-medium">Nomor Tiket</label>
                    <p class="w-full" x-text="properties.data.pesanan.nomor_pemesanan"></p>
                </div>
                <div class="w-full border-b py-2 mb-2">
                    <label class="text-gray-700 font-medium">Tanggal Tiket Dipesan</label>
                    <p class="w-full " x-text="tanggalToIndo(properties.data.pesanan.tanggal_pemesanan)"></p>
                </div>
                <div class="w-full border-b py-2 mb-2">
                    <label class="text-gray-700 font-medium">Tanggal Keberangkatan</label>
                    <p class="w-full " x-text="tanggalToIndo(properties.data.pesanan.tanggal_keberangkatan) + ' ' + properties.data.pesanan.jam_keberangkatan + ' WIB'"></p>
                </div>
                <div class="w-full border-b py-2 mb-2">
                    <label class="text-gray-700 font-medium">Rute Perjalanan</label>
                    <p class="w-full " x-text="properties.data.pesanan.keberangkatan"></p>
                </div>
                <div class="w-full border-b py-2 mb-2">
                    <label class="text-gray-700 font-medium">Mobil dan Driver</label>
                    <p class="w-full " x-text="'-'"></p>
                </div>
                <div class="w-full border-b py-2 mb-2">
                    <label class="text-gray-700 font-medium">Nomor Kursi</label>
                    <p class="w-full " x-text="properties.data.pesanan.list_kursi_dipesan.map(item => item.nomor_kursi).join(', ')"></p>
                </div>
                <div class="w-full border-b py-2 mb-2">
                    <label class="text-gray-700 font-medium">Tagihan</label>
                    <p class="w-full ">
                        <span x-text="properties.data.pesanan.list_kursi_dipesan.length"></span>
                        kursi @<span x-text="currencyToRupiah(properties.data.pesanan.total_tarif / properties.data.pesanan.list_kursi_dipesan.length)"></span>
                        =
                        <span x-text="currencyToRupiah(properties.data.pesanan.total_tarif)"></span>
                    </p>
                </div>
                <div class="w-full border-b py-2 mb-2">
                    <label class="text-gray-700 font-medium">Tiket</label>
                    <a href="" class="text-red-500 underline text-sm block hover:text-red-600">Unduh untuk melihat.</a>
                </div>
            </div>
        </div>
        <div>
            <div class="rounded-lg border bg-white p-8 mb-4">
                <h6 class="rounded-tl rounded-tr font-sans text-xl font-semibold text-gray-700 mb-2">Informasi Pemesan</h6>
                <div class="w-full border-b py-2 mb-2">
                    <label class="text-gray-700 font-medium">Atas Nama Pemesan</label>
                    <p class="w-full " x-text="properties.data.pesanan.nama_pemesanan ?? '-'"></p>
                </div>
                <div class="w-full border-b py-2 mb-2">
                    <label class="text-gray-700 font-medium">Kontak Pemesan</label>
                    <p class="w-full " x-text="properties.data.pesanan.kontak_pemesanan == '' ?? '-'"></p>
                </div>
                <div class="w-full border-b py-2 mb-2">
                    <label class="text-gray-700 font-medium">Alamat Jemput</label>
                    <p class="w-full " x-text="properties.data.pesanan.titik_jemput ?? '-'"></p>
                </div>
                <div class="w-full border-b py-2 mb-2">
                    <label class="text-gray-700 font-medium">Informasi Akun Pemesan</label>
                    <p class="w-full " x-text="'-'"></p>
                </div>
            </div>
            <div class="rounded-lg border bg-white p-8 mb-4">
                <h6 class="rounded-tl rounded-tr font-sans text-xl font-semibold text-gray-700 mb-2">Informasi Pembayaran</h6>
                <div class="w-full border-b py-2 mb-2">
                    <label class="text-gray-700 font-medium">Atas Nama Pembayaran</label>
                    <p class="w-full " x-text="properties.data.pesanan.nama_pembayaran ?? '-'"></p>
                </div>
                <div class="w-full border-b py-2 mb-2">
                    <label class="text-gray-700 font-medium">Bank Pembayaran</label>
                    <p class="w-full " x-text="properties.data.pesanan.bank_pembayaran ?? '-'"></p>
                </div>
                <div class="w-full border-b py-2 mb-2">
                    <label class="text-gray-700 font-medium">Nominal Dibayarkan</label>
                    <p class="w-full " x-text="currencyToRupiah(properties.data.pesanan.total_dibayarkan)"></p>
                </div>
                <div class="w-full border-b py-2 mb-2">
                    <label class="text-gray-700 font-medium">Bukti Pembayaran</label>
                    <a x-show="properties.data.pesanan.status_bukti_pembayaran.toLowerCase() == 'valid'" :href="properties.sites.api_url + '/api/admin/pesanan/unduh-bukti-pembayaran?nomor=' + properties.data.pesanan.nomor_pemesanan" class="text-red-500 underline text-sm block hover:text-red-600">Unduh untuk melihat.</a>
                    <span x-show="properties.data.pesanan.status_bukti_pembayaran.toLowerCase() != 'valid'" class="block">-</span>
                </div>
            </div>
            <template x-if="properties.data.pesanan.status_bukti_pembayaran.toLowerCase() == 'unconfirmed'">
                <div class="flex justify-end gap-2 mt-8" >
                    <button @click.prevent="konfirmasiBuktiPembayaran(1, $event.target)" class="cursor-pointer text-white bg-green-500 hover:bg-green-600 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-4 py-4 text-center mr-3 md:mr-0">Konfirmasi Bukti Pembayaran</button>
                    <button @click.prevent="konfirmasiBuktiPembayaran(-1, $event.target)" class="cursor-pointer text-white bg-red-500 hover:bg-red-600 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-4 py-4 text-center mr-3 md:mr-0">Tolak Bukti Pembayaran</button>
                </div>
            </template>
        </div>
    </div>
</main>

<script type="text/javascript">
    document.addEventListener('alpine:init', () => {
        // @TODO: separate to file
        const actions = {
            "konfirmasiBuktiPembayaran": function(status, elem) {
                const innerText = elem.innerText;
                this.buttonLoading(elem);

                axios
                    .post(`${this.properties.sites.api_url}/api/admin/pesanan/status-bukti-pembayaran?nomor=${this.properties.data.pesanan.nomor_pemesanan}&status=${status}`)
                    .then(res => {
                        window.location.reload();
                    })
                    .catch(err => console.error(err))
            }
        };

        const utils = {
            "currencyToRupiah": function (number) {
                return 'Rp ' + (new Intl.NumberFormat('id-Id', {"maximumSignificantDigits": 3}).format(number));
            },
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
                elem.classList.add('opacity-50');
                elem.classList.add('cursor-not-allowed');
            },
            "buttonRemoveLoading": function (elem, statusText) {
                elem.disabled = false;
                elem.innerText = statusText;
                elem.classList.remove('bg-gray-700');
                elem.classList.remove('hover:bg-gray-700');
                elem.classList.remove('focus:ring-gray-700');
                elem.classList.remove('opacity-50');
                elem.classList.remove('cursor-not-allowed');
            }
        };

        Alpine.data('container',
            () => ({
                ...actions,
                ...utils,
                "properties": {
                    "sites": {
                        "api_url": "<?= site_url() ?>",
                        "page_title": "Tiket Nomor: <?= $_GET['nomor'] ?>",
                    },
                    "errors": {},
                    "data": {
                        "pesanan": JSON.parse('<?= json_encode($pesanan->toArray()) ?>')
                    }
                },
                "init": function() {

                }
            })
        );
    });
</script>