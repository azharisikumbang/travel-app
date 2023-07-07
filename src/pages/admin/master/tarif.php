<?php

$app = app()->getManager();
$listKategoriPenumpang = $app->getService('KategoriPelangganService')->listTipePenumpang();
$listDaerah = $app->getService('DaerahOperasionalService')->listDaerahOperasional();
$listTarifByKategori = $app->getService('TarifService')->listTarifByKategori(50);

?>
<nav class="block w-full max-w-full bg-transparent text-white shadow-none transition-all px-0 py-1 border-b-2">
    <div class="flex flex-col-reverse justify-between gap-6 md:flex-row md:items-center">
        <h2 class="block antialiased tracking-normal font-sans text-2xl font-semibold leading-relaxed text-gray-900">Data Tarif / Tiket</h2>
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
    <div class="mb-4 grid grid-cols-1 gap-6 xl:grid-cols-3">
        <div class="xl:col-span-2">
            <?php if($listTarifByKategori): ?>
                <?php foreach($listTarifByKategori as $listTarif): ?>
                <div class="mb-12">
                    <h5 class="block antialiased tracking-normal font-sans text-xl font-semibold leading-relaxed text-gray-900 mb-2">Kategori: <?= $listTarif['tipe_penumpang'] ?></h5>
                    <div class="grid grid-cols-3 gap-4">
                        <?php foreach($listTarif['list_tarif'] as $tarif): ?>
                        <div class="bg-clip-border rounded-xl bg-white text-gray-700 shadow-md p-6">
                            <h6 class="block antialiased tracking-normal font-sans text-base font-semibold leading-relaxed text-gray-900"><?= $tarif['kota_asal']['nama_kota'] ?> - <?= $tarif['kota_tujuan']['nama_kota'] ?></h6>
                            <p class="font-sans text-base text-gray-500 mb-2 block">Rp <?= rupiah($tarif['tarif']) ?></p>
                            <div class="border-t border-gray-200 pt-4">
                                <button class="bg-orange-400 text-white text-sm rounded px-2 py-1 font-sans center">Edit</button>
                                <button class="bg-red-700 text-white text-sm rounded px-2 py-1 font-sans center">Hapus</button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center">
                    Tidak ada data.
                </div>
            <?php endif; ?>
        </div>
        <div>
            <div class="flex flex-col bg-clip-border rounded-xl bg-white text-gray-700 shadow-md p-6">
                <div class="relative bg-clip-border rounded-xl overflow-hidden bg-transparent text-gray-700 shadow-none m-0">
                    <h6 class="block antialiased tracking-normal font-sans text-base font-semibold leading-relaxed text-gray-900 mb-2">Tambahkan Kategori Baru</h6>
                </div>
                <form action="<?= site_url('admin/master/tarif/simpan') ?>" class="mt-4" method="post">
                    <div class="w-full min-w-[200px] mb-4">
                        <label for="" class="font-sans text-base text-gray-500 mb-2 block">Kategori Penumpang</label>
                        <select name="kategori" class="w-full bg-transparent text-gray-700 font-sans font-normal outline outline-0 border-2 text-sm px-3 py-3 rounded-md border-gray-200 focus:border-gray-400">
                            <option value="0">--PILIH KATEGORI--</option>
                            <?php foreach ($listKategoriPenumpang as $kategori): ?>
                            <option value="<?= $kategori->getId() ?>"><?= $kategori->getTipePenumpang() ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="w-full min-w-[200px] mb-4">
                        <label for="" class="font-sans text-base text-gray-500 mb-2 block">Daerah (Loket) Asal Keberangkatan</label>
                        <select name="asal" class="w-full bg-transparent text-gray-700 font-sans font-normal outline outline-0 border-2 text-sm px-3 py-3 rounded-md border-gray-200 focus:border-gray-400">
                            <option value="0">--PILUH DAERAH KEBERANGKATAN--</option>
                            <?php foreach ($listDaerah as $daerah): ?>
                                <option value="<?= $daerah->getId() ?>"><?= $daerah->getNamaKota() ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="w-full min-w-[200px] mb-4">
                        <label for="" class="font-sans text-base text-gray-500 mb-2 block">Daerah (Loket) Tujuan Keberangkatan</label>
                        <select name="tujuan" class="w-full bg-transparent text-gray-700 font-sans font-normal outline outline-0 border-2 text-sm px-3 py-3 rounded-md border-gray-200 focus:border-gray-400">
                            <option value="0">--PILIH DAERAH TUJUAN--</option>
                            <?php foreach ($listDaerah as $daerah): ?>
                                <option value="<?= $daerah->getId() ?>"><?= $daerah->getNamaKota() ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="w-full min-w-[200px] mb-4">
                        <label for="" class="font-sans text-base text-gray-500 mb-2 block">Tarif (Rupiah)</label>
                        <input type="number" min="0" value="0" name="tarif" class="w-full bg-transparent text-gray-700 font-sans font-normal outline outline-0 border-2 text-sm px-3 py-3 rounded-md border-gray-200 focus:border-gray-400">
                    </div>
                    <div class="w-full min-w-[200px] mt-8">
                        <button class="bg-green-500 w-full text-white rounded py-4 font-sans center">Tambahkan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>