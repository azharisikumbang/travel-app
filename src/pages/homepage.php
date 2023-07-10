<?php

$listTiketAvailable = app()->getManager()->getService('TiketService')->listTarifByKategori();

html_require_component('navbar');

?>
<!-- main -->
<main x-data="listAvailableTiket">
    <div class="bg-gray-100">
        <div class="max-w-screen-xl mx-auto py-20">
            <div class="text-center mb-8">
                <h2 class="block antialiased tracking-normal font-sans text-xl font-bold leading-relaxed text-gray-900">Tiket Keberangkatan</h2>
                <p class="font-sans text-gray-600">Nikmati Perjalanan Dengan Tarif Sesuai Kantong.</p>
            </div>
            <div class="flex col-span-3 mx-auto justify-center mb-12">
                <template x-for="tiket in listTiket">
                    <div class="py-2 px-12 border-gray-800 border text-center cursor-pointer hover:bg-gray-800 hover:text-white" x-text="tiket[0].nama" :class="{ 'bg-gray-800 text-white': active == tiket[0].kategori_id }" @click="active = tiket[0].kategori_id"></div>
                </template>
            </div>
            <div>
                <template x-for="tiket in listTiket">
                    <div class="grid grid-cols-3 gap-4 justify-center" x-show="active == tiket[0].kategori_id">
                        <template x-for="t in tiket">
                            <div class="bg-clip-border rounded-xl bg-white text-gray-700 shadow-md p-6 flex flex-row items-center justify-between content-center">
                                <div class="basis-3/4">
                                    <h6 class="font-sans text-base text-gray-500 block">
                                        <span x-text="t.tiket.rute.asal.nama_kota"></span> - <span x-text="t.tiket.rute.tujuan.nama_kota"></span>
                                    </h6>
                                    <p class="block antialiased tracking-normal font-sans text-base font-semibold leading-relaxed text-gray-900" x-text="currencyToRupiah(t.tiket.tarif)"></p>
                                </div>
                                <div class="basis-1/4">
                                    <a :href="'<?= site_url('pesan?tiket=') ?>' + t.tiket.id" class="text-white bg-gray-700 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-4 py-2 text-center mr-3 md:mr-0">Pesan</a>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
        </div>
    </div>
</main>
<!-- end:main -->
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('listAvailableTiket', () => ({
            active: -1,
            listTiket: JSON.parse('<?= json_encode($listTiketAvailable) ?>'),
            currencyToRupiah: function (number) {
                return 'Rp ' + (new Intl.NumberFormat('id-Id', {"maximumSignificantDigits": 3}).format(number));
            },
            init() {
                if (this.listTiket[0].length > 0) this.active = this.listTiket[0][0].kategori_id;
            }
        }));
    });
</script>

