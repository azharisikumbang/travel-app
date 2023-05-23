<?php html_require_component('navbar'); ?>
<!-- main -->
<main x-data="listAvailableTiket">
    <div class="bg-gray-100">
        <div class="max-w-screen-xl mx-auto py-20">
            <div class="text-center mb-8">
                <h2 class="block antialiased tracking-normal font-sans text-xl font-bold leading-relaxed text-gray-900">Daftar Tiket Keberangkatan</h2>
            </div>
            <div class="flex col-span-3 mx-auto justify-center mb-12">
                <template x-for="tiket in listTiket">
                    <div class="py-2 w-32 border-gray-800 border text-center cursor-pointer" x-text="tiket.tipe_penumpang" :class="{ 'bg-gray-800 text-white': active == tiket.tipe_penumpang_id }" @click="active = tiket.tipe_penumpang_id"></div>
                </template>
            </div>
            <div>
                <template x-for="tiket in listTiket">
                    <div class="grid grid-cols-3 gap-4 justify-center" x-show="active == tiket.tipe_penumpang_id">
                        <template x-for="t in tiket.list_tarif">
                            <div class="bg-clip-border rounded-xl bg-white text-gray-700 shadow-md p-6 flex flex-row items-center justify-between content-center">
                                <div class="basis-3/4">
                                    <h6 class="font-sans text-base text-gray-500 block">
                                        <span x-text="t.kota_asal.nama_kota"></span> - <span x-text="t.kota_tujuan.nama_kota"></span>
                                    </h6>
                                    <p class="block antialiased tracking-normal font-sans text-base font-semibold leading-relaxed text-gray-900">Rp <span x-text="t.tarif"></span></p>
                                </div>
                                <div class="basis-1/4">
                                    <a x-bind:href="'<?= site_url('pesan?tarif=') ?>' + t.id" class="text-white bg-gray-700 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-4 py-2 text-center mr-3 md:mr-0">Pesan</a>
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
            active: 3,
            listTiket: [],
            init() {
                axios.get("<?= site_url('api/list-tiket?order=kategori') ?>")
                    .then(res => this.listTiket = res.data)
            }
        }));
    });
</script>

