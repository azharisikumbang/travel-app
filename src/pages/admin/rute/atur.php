<?php

/** @var $rute RuteHarian */
/** @var $jam JamKeberangkatan */
/** @var $mobil Mobil */

$manager = app()->getManager();
$listRute = $manager->getService('RuteService')->listRuteTersedia();
$listMobil = $manager->getService('MobilService')->listMobilOperasional();
$listJamKeberangkatan = $manager->getService('JamKeberangkatanService')->listJamKeberangkatan();

?>
<main x-data="container">
    <nav class="block w-full max-w-full bg-transparent text-white shadow-none transition-all px-0 py-1 border-b-2">
        <div class="flex flex-col-reverse justify-between gap-6 md:flex-row md:items-center">
            <h2 class="block antialiased tracking-normal font-sans text-2xl font-semibold leading-relaxed text-gray-900">Rute Hati Ini</h2>
            <div class="flex items-center">
                <span class="font-sans text-gray-500"><?php echo tanggal(date_create()) ?></span>
            </div>
        </div>
    </nav>
    <div id="content" class="mt-8 w-full overflow-hidden">
        <?php if(session('temp')): ?>
            <div class="mb-4 block w-full text-base font-regular px-4 py-4 rounded-lg bg-green-500 text-white">
                <?php echo session('temp')['message'] ?>
            </div>
        <?php endif; ?>
        <div class="mb-4 bg-sky-500 text-white border-2 p-4 rounded-lg">
            <p class="font-sans">
                <span class="font-semibold">Informasi!</span>
                Rute yang tidak diupdate secara harian akan tetap menggunakan pengaturan rute terakhir digunakan. <br>
                Untuk kesamaan informasi dengan di lapangan, mohon update informasi rute setiap hari.
            </p>
        </div>
        <div class="w-full border border-gray-300 shadow-lg rounded-lg p-4 bg-white">
            <div class="flex justify-between">
                <h2 class="font-semibold font-sans text-xl">Atur Rute: <?php echo tanggal(date_create()) ?></h2>
                <button type="button" @click="properties.sites.show_form = !properties.sites.show_form">
                    <svg class="svg-icon h-6 text-gray-200" viewBox="0 0 20 20">
                        <path d="M3.314,4.8h13.372c0.41,0,0.743-0.333,0.743-0.743c0-0.41-0.333-0.743-0.743-0.743H3.314
                            c-0.41,0-0.743,0.333-0.743,0.743C2.571,4.467,2.904,4.8,3.314,4.8z M16.686,15.2H3.314c-0.41,0-0.743,0.333-0.743,0.743
                            s0.333,0.743,0.743,0.743h13.372c0.41,0,0.743-0.333,0.743-0.743S17.096,15.2,16.686,15.2z M16.686,9.257H3.314
                            c-0.41,0-0.743,0.333-0.743,0.743s0.333,0.743,0.743,0.743h13.372c0.41,0,0.743-0.333,0.743-0.743S17.096,9.257,16.686,9.257z"></path>
                    </svg>
                </button>
            </div>
            <template x-if="properties.sites.show_form">
                <div class="block border-t mt-4 py-4">
                    <table class="w-full">
                        <thead>
                        <tr>
                            <th class="border">No</th>
                            <th class="border">Rute</th>
                            <th class="border">Mobil</th>
                            <th class="border">Jam</th>
                            <th class="border">Terakhir Diupdate</th>
                            <th class="border py-2 w-32">Update</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($listRute as $index => $rute): ?>
                            <tr>
                                <td class="border py-2 text-center w-12"><?= $index + 1 ?></td>
                                <td class="border py-2 text-center">
                                    <?= $rute->getAsal()->getNamaKota() . ' - ' . $rute->getTujuan()->getNamaKota() ?>
                                </td>
                                <td class="border py-2 w-96 text-center">
                                    <select class="cursor-pointer px-2 py-2 outline-none text-center" name="mobil[<?= $rute->getId() ?>]">
                                        <option value="-1">-- Pilih Mobil --</option>
                                        <?php foreach($listMobil as $mobil): ?>
                                            <option value="<?= $mobil->getId() ?>" <?= $mobil->getId() == $rute->getMobil()?->getId() ? 'selected' : '' ?>>
                                                <?= $mobil->getMerk() . ' / ' . $mobil->getPlatNomor()  . ' / ' . $mobil->getDriver()->getNamaLengkap() ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td class="border py-2 w-72 text-center">
                                    <select class="cursor-pointer px-2 py-2 outline-none text-center" name="jam[<?= $rute->getId() ?>]">
                                        <option value="-1">-- Pilih Jam Keberangkatan --</option>
                                        <?php foreach($listJamKeberangkatan as $jam): ?>
                                            <option value="<?= $jam->getId() ?>" <?= $jam->getId() == $rute->getJamKeberangkatan()?->getId() ? 'selected' : '' ?>>
                                                <?= $jam->getJam() . ' WIB / ' . $jam->getAlias() ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td class="border py-2 text-center w-48" id="last-updated-<?= $rute->getId() ?>">
                                    <?= tanggal($rute->getLastUpdated()); ?>
                                </td>
                                <td class="border py-2 text-center">
                                    <button class="bg-orange-500 rounded text-sm text-white py-1 px-4 hover:bg-orange-700" @click="updateRute(<?= $rute->getId() ?>)">Update</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="6" class="border">
                                <button class="w-full py-2 text-center hover:bg-gray-100 text-gray-600">+ Tambah</button>
                            </td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </template>
        </div>
        <div>

        </div>
    </div>
</main>
<script type="text/javascript">
    document.addEventListener('alpine:init', () => {
        const actions = {
            "updateRute": function (ruteId) {
                if(!confirm('Anda yakin ingin mengupdate rute ini ?')) return;

                this.buttonLoading(this.$event.target, 'mengupdate..');

                let selectedMobilId = document.querySelector(`select[name='mobil[${ruteId}]']`).value;
                let selectedJamId = document.querySelector(`select[name='jam[${ruteId}]']`).value;

                let formData = new FormData();
                formData.append('rute', ruteId);
                formData.append('jam', selectedJamId);
                formData.append('mobil', selectedMobilId);

                axios
                    .post(this.properties.sites.api_url + '/api/admin/rute/update', formData)
                    .then(response => {
                        document.getElementById('last-updated-' + ruteId).innerText = response.data.data.last_updated;
                        this.buttonRemoveLoading(this.$event.target, 'Berhasil !');
                    })
                    .catch(err => console.error(err));
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
            }
        };

        Alpine.data('container',
            () => ({
                ...actions,
                ...utils,
                "properties": {
                    "sites": {
                        "api_url": "<?= site_url() ?>",
                        "show_form": true
                    },
                    "errors": {},
                    "data": {
                        "list_rute": [],
                        "list_jam_keberangkatan": [],
                        "list_mobil": []
                    },
                    "form": []
                },
                "init": async function() {
                    // this.properties.data.list_rute = await this.loadListRute();
                    // this.properties.data.list_jam_keberangkatan = await this.loadListJamKeberangkatan();
                    // this.properties.data.list_mobil = await this.loadListMobil();
                }
            })
        );
    });
</script>