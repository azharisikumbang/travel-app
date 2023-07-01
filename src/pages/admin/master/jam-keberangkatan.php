<?php $listJamKeberangkatan = app()->getManager()->getService('JamKeberangkatanService')->listJamKeberangkatan();?>
<nav class="block w-full max-w-full bg-transparent text-white shadow-none transition-all px-0 py-1 border-b-2">
    <div class="flex flex-col-reverse justify-between gap-6 md:flex-row md:items-center">
        <h2 class="block antialiased tracking-normal font-sans text-2xl font-semibold leading-relaxed text-gray-900">Jam Keberangkatan</h2>
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
            <div class="flex flex-col bg-clip-border rounded-xl bg-white text-gray-700 shadow-md overflow-hidden">
                <div class="relative bg-clip-border rounded-xl overflow-hidden bg-transparent text-gray-700 shadow-none m-0 flex items-center justify-between p-6">
                    <div>
                        <h6 class="block antialiased tracking-normal font-sans text-base font-semibold leading-relaxed text-gray-900 mb-1">Terdapat <?= count($listJamKeberangkatan) ?> jenis jam keberangkatan</h6>
                    </div>
                </div>
                <div class="p-6 overflow-x-scroll px-0 pt-0 pb-0">
                    <table class="w-full min-w-[640px] table-auto">
                        <thead>
                        <tr>
                            <th class="border-b border-gray-200 py-3 px-6 text-left">
                                <p class="block antialiased font-sans text-[11px] font-medium uppercase text-gray-400">No</p>
                            </th>
                            <th class="border-b border-gray-200 py-3 px-6 text-left">
                                <p class="block antialiased font-sans text-[11px] font-medium uppercase text-gray-400">Jam Berangkat (WIB)</p>
                            </th>
                            <th class="border-b border-gray-20 py-3 px-6 text-left"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if(count($listJamKeberangkatan) < 1): ?>
                            <tr>
                                <td class="py-3 px-5 border-b border-gray-200 w-2" colspan="3">
                                    <p class="block antialiased font-sans text-sm leading-normal text-gray-600 text-center">Tidak ada data.</p>
                                </td>
                            </tr>
                        <?php else:
                            $no = 1;
                            foreach ($listJamKeberangkatan as $jamKeberangkatan): ?>
                                <tr>
                                    <td class="py-3 px-5 border-b border-gray-200 w-2">
                                        <p class="block antialiased font-sans text-sm leading-normal text-gray-600 text-center"><?= $no++; ?></p>
                                    </td>
                                    <td class="py-3 px-5 border-b border-gray-200">
                                        <p class="block antialiased font-sans text-xs font-medium text-gray-900 font-bold" ><?= $jamKeberangkatan->getJam(true); ?> WIB / <?= strtoupper($jamKeberangkatan->getAlias()); ?></p>
                                    </td>
                                    <td class="py-3 px-5 border-b border-gray-200 flex justify-end">
                                        <button class="bg-orange-400 text-white rounded px-4 py-1 font-sans center">Edit</button>
                                    </td>
                                </tr>
                            <?php
                            endforeach;
                        endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div>
            <div class="flex flex-col bg-clip-border rounded-xl bg-white text-gray-700 shadow-md p-6">
                <div class="relative bg-clip-border rounded-xl overflow-hidden bg-transparent text-gray-700 shadow-none m-0">
                    <h6 class="block antialiased tracking-normal font-sans text-base font-semibold leading-relaxed text-gray-900 mb-2">Tambahkan Kategori Baru</h6>
                </div>
                <form action="<?= site_url('admin/master/jam-keberangkatan/simpan') ?>" class="mt-4" method="post">
                    <div class="w-full min-w-[200px] mb-4">
                        <label for="" class="font-sans text-base text-gray-500 mb-2 block">Jam Keberangkatan (am=pagi-siang, pm=siang-malam)</label>
                        <input type="time" name="jam" class="w-full bg-transparent text-gray-700 font-sans font-normal outline outline-0 border-2 text-sm px-3 py-3 rounded-md border-gray-200 focus:border-gray-400" autofocus>
                    </div>
                    <div class="w-full min-w-[200px] mb-4">
                        <label for="" class="font-sans text-base text-gray-500 mb-2 block">Alias / Label (cth: PAGI.)</label>
                        <input type="text" name="alias" class="w-full bg-transparent text-gray-700 font-sans font-normal outline outline-0 border-2 text-sm px-3 py-3 rounded-md border-gray-200 focus:border-gray-400" autofocus>
                    </div>
                    <div class="w-full min-w-[200px] mt-8">
                        <button class="bg-green-500 w-full text-white rounded py-4 font-sans center">Tambahkan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>