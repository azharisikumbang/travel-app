<?php

if (false === session()->isAuthenticatedAs('pelanggan')) html_unauthorized();
$akun = session()->auth();
$me = app()->getManager()->getService('PelangganService')->informasiSaya($akun);
$listKategoriPelanggan = app()->getManager()->getService('KategoriPelangganService')->listKategoriPelanggan();

?>
<main x-data="container">
    <nav class="block w-full max-w-full bg-transparent text-white shadow-none transition-all px-0 py-1 border-b-2">
        <div class="flex flex-col-reverse justify-between gap-6 md:flex-row md:items-center">
            <h2 class="block antialiased tracking-normal font-sans text-2xl font-semibold leading-relaxed text-gray-900">Pengaturan Akun</h2>
        </div>
    </nav>
    <div id="content" class="mt-8 w-full overflow-hidden flex gap-8">
        <div class="mb-4 w-4/12">
            <?php if(session('temp')): ?>
                <div class="mb-4 block w-full text-base font-regular px-4 py-4 rounded-lg bg-<?= session('temp')['status'] ? 'green' : 'yellow'  ?>-500 text-white">
                    <?php echo session('temp')['message'] ?>
                </div>
            <?php endif; ?>
            <form action="<?= site_url('pelanggan/pengaturan/akun/simpan'); ?>" method="post" enctype="multipart/form-data">
                <div class="w-full min-w-[200px] mb-4">
                    <label for="" class="font-sans text-base text-gray-500 mb-2 block">Nama Lengkap</label>
                    <input type="text" name="nama" class="w-full text-gray-700 font-sans font-normal outline outline-0 border-2 text-sm px-3 py-3 rounded-md border-gray-200 focus:border-gray-400" value="<?= $me->getNama() ?>">
                </div>
                <div class="w-full min-w-[200px] mb-4">
                    <label for="" class="font-sans text-base text-gray-500 mb-2 block">No Handphone</label>
                    <input value="<?= $me->getKontak() ?>" type="text" name="kontak" class="w-full bg-white text-gray-700 font-sans font-normal outline outline-0 border-2 text-sm px-3 py-3 rounded-md border-gray-200 focus:border-gray-400">
                </div>
                <div class="w-full min-w-[200px] mb-4">
                    <label for="" class="font-sans text-base text-gray-500 mb-2 block">Status Pelanggan</label>
                    <select @change="showFormIdentitas" name="kategori" class="w-full bg-white text-gray-700 font-sans font-normal outline outline-0 border-2 text-sm px-3 py-3 rounded-md border-gray-200 focus:border-gray-400">
                        <?php foreach ($listKategoriPelanggan as $kategori): ?>
                        <option value="<?= $kategori->getId() ?>" <?= $kategori->getId() == $me->getKategoriPelanggan()->getId() ? 'selected': '' ?>><?= $kategori->getKategori() ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="w-full min-w-[200px] mb-4" x-show="properties.show.form_identitas">
                    <label for="" class="font-sans text-base text-gray-500 mb-2 block">Kartu Identitas Mahasiswa</label>
                    <input name="photo_identitas" type="file" class="w-full">
                </div>
                <div class="w-full min-w-[200px] mt-8 flex justify-end">
                    <button type="submit" class="bg-gray-700 text-white rounded py-2 px-8 font-sans center">Simpan</button>
                </div>
            </form>
        </div>
        <?php if ($me->getPhotoIdentitas()) : ?>
        <div class="mb-4 w-8/12">
            <p>Gagal menampilkan preview kartu identitas. silahkan <a class="text-red-400 hover:underline" href="<?= site_url('api/pelanggan/akun/unduh-photo-identitas') ?>">klik disini</a> untuk melihat.</p>
        </div>
        <?php endif; ?>
    </div>
</main>
<script type="text/javascript">
    document.addEventListener('alpine:init', () => {
        // @TODO: separate to file
        const actions = {
            "showFormIdentitas": function () {
                let value = this.$event.target.value;

                if (this.properties.data.list_kategori_pelanggan[this.properties.data.umum_index].id == value)
                    this.properties.show.form_identitas = false;
                else this.properties.show.form_identitas = true;


            }
        };

        const utils = {};

        Alpine.data('container',
            () => ({
                ...actions,
                ...utils,
                "properties": {
                    "show": {
                        "form_identitas": false
                    },
                    "data": {
                        "list_kategori_pelanggan": JSON.parse('<?= json_encode(array_map(fn ($item) => $item->toArray(), $listKategoriPelanggan)) ?>'),
                        "umum_index": -1
                    }
                },
                "init": function() {
                    this.properties.data.umum_index = this.properties.data.list_kategori_pelanggan.findIndex(item => item.kategori.toLowerCase() == 'umum');
                }
            })
        );
    });
</script>
