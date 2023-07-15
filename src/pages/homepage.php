<?php

$manager = app()->getManager();
$listTiketAvailable = $manager->getService('TiketService')->listTarifByKategori();
$listDaerahOperasional = $manager->getService('DaerahOperasionalService')->listDaerahOperasional();

html_require_component('navbar');

?>
<!-- main -->
<main x-data="listAvailableTiket">
    <div class="relative pt-16 pb-32 flex content-center items-center justify-center" style="min-height: 75vh">
        <div class="absolute top-0 w-full h-full bg-center bg-cover" style="background-image: url('https://images.unsplash.com/photo-1638199406429-f0c6cf5de006?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&w=1024&q=80');">
            <span class="w-full h-full absolute opacity-25 bg-black"></span>
        </div>
        <div class="container relative mx-auto">
            <div class="items-center flex flex-wrap">
                <div class="w-full lg:w-6/12 px-4 ml-auto mr-auto text-center">
                    <div>
                        <h1 class="text-white font-semibold text-5xl">
                            PT. SOREK WISATA TRANSPORT
                        </h1>
                        <p class="mt-4 text-lg text-slate-200">
                            Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                        </p>
                        <div class="mt-12">
                            <div class="w-full grid grid-cols-3 gap-4">
                                <div>
                                    <input type="date" class="cursor-pointer w-full bg-white text-gray-700 font-sans font-normal outline outline-0 border-2 text-sm pl-2 pr-4 py-3 rounded-md border-gray-200 focus:border-gray-400 text-center">
                                </div>
                                <select name="asal"
                                        class="cursor-pointer w-full bg-white text-gray-700 font-sans font-normal outline outline-0 border-2 text-sm pl-2 pr-4 py-3 rounded-md border-gray-200 focus:border-gray-400 text-center">
                                    <option value="-1">Asal</option>
                                    <?php
                                    if($listDaerahOperasional):
                                    foreach ($listDaerahOperasional as $daerah): ?>
                                    <option value="<?= $daerah->getId() ?>"><?= $daerah->getNamaKota() ?></option>
                                    <?php endforeach; endif; ?>
                                </select>
                                <select name="tujuan"
                                        class="cursor-pointer w-full bg-white text-gray-700 font-sans font-normal outline outline-0 border-2 text-sm pl-2 pr-4 py-3 rounded-md border-gray-200 focus:border-gray-400 text-center">
                                    <option value="-1">Tujuan</option>
                                    <?php
                                    if($listDaerahOperasional):
                                        foreach ($listDaerahOperasional as $daerah): ?>
                                            <option value="<?= $daerah->getId() ?>"><?= $daerah->getNamaKota() ?></option>
                                        <?php endforeach; endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="mt-8">
                            <button type="submit" class="block text-white bg-yellow-500 hover:bg-yellow-600 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-4 py-4 text-center mr- mx-auto w-64">
                                Pesan Sekarang
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
    <div class="py-16">
        <div class="container mx-auto flex justify-between text-slate-600 antialiased font-sans">
            <div>
                <h3 class="text-3xl font-semibold">PT Sorek Wisata Transport</h3>
                <p class="text-slate-500 mb-2">Jl. Perinstis Kemerdekaan no. 13 Pekanbaru, Riau 22345</p>
                <p class="text-slate-500">
                    ☎ Pusat : 0812 6828 0330 <br>
                    ☎ Sorek : 0813 9888 5884 / 0813 2342 4200 <br>
                    ☎ Pangkalan Kerici : 0813 9888 5884 / 0813 2342 4100  <br>
                    ☎ Padang : 0822 6888 5884 / 0813 2342 4100
                </p>
                <div class="flex mt-4 gap-4 border-t pt-4">
                    <!--facebook-->
                    <a href="#" class="">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-6 w-6"
                            fill="currentColor"
                            style="color: #1877f2"
                            viewBox="0 0 24 24">
                        <path
                        d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z" />
                        </svg>
                    </a>
                    <a href="">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-6 w-6"
                            fill="currentColor"
                            style="color: #c13584"
                            viewBox="0 0 24 24">
                            <path
                                d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                        </svg>
                    </a>
                    <a href="">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-6 w-6"
                            fill="currentColor"
                            style="color: #128c7e"
                            viewBox="0 0 24 24">
                            <path
                                d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />
                        </svg>
                    </a>
                </div>
            </div>
            <div class="w-3/5">
                <div class="mapouter"><div class="gmap_canvas"><iframe class="gmap_iframe" width="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?width=600&amp;height=400&amp;hl=en&amp;q=pekanbaru&amp;t=&amp;z=14&amp;ie=UTF8&amp;iwloc=B&amp;output=embed"></iframe><a href="https://connectionsgame.org/">Connections Game</a></div><style>.mapouter{position:relative;text-align:right;width:100%;height:400px;}.gmap_canvas {overflow:hidden;background:none!important;width:100%;height:400px;}.gmap_iframe {height:400px!important;}</style></div>
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

