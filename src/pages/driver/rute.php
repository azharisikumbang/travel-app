<?php

/** @var $pesanan Pesanan  */
$app = app()->getManager();
$driver = $app->getService('DriverService')->findByAkun(session()->auth());
$listRuteDriver = $app->getService('DriverService')->listRuteSaya($driver);

?>
<div>
    <div class="w-full flex justify-between border-b pb-1 mb-4">
        <h2 class="antialiased tracking-normal font-sans text-2xl font-semibold leading-relaxed text-gray-900">Daftar Rute Aktif</h2>
    </div>
    <div class="p-4 rounded shadow bg-white mt-8">
        <table class="w-full min-w-[640px] table-auto">
            <thead>
                <tr>
                    <th class="border-b border-gray-200 py-3 px-6 text-left w-2">
                        <p class="block antialiased font-sans text-[11px] font-medium uppercase text-gray-400">No</p>
                    </th>
                    <th class="border-b border-gray-200 py-3 px-6 text-left">
                        <p class="block antialiased font-sans text-[11px] font-medium uppercase text-gray-400">Rute</p>
                    </th>
                    <th class="border-b border-gray-200 py-3 px-6 text-left">
                        <p class="block antialiased font-sans text-[11px] font-medium uppercase text-gray-400">Mobil</p>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($listRuteDriver as $index => $rute): ?>
                    <tr>
                        <td class="py-3 px-5 border-b border-gray-200 w-2">
                            <p class="block antialiased font-sans text-sm leading-normal text-gray-600 text-center"><?= $index + 1 ?></p>
                        </td>
                        <td class="py-3 px-5 border-b border-gray-200 text-left">
                            <p class="block antialiased font-sans text-sm leading-normal text-gray-600"><?= $rute['asal'] . ' - ' . $rute['tujuan'] ?></p>
                        </td>
                        <td class="py-3 px-5 border-b border-gray-200">
                            <p class="block antialiased font-sans text-sm leading-normal text-gray-600"><?= $rute['merk'] . ' ' . $rute['plat_nomor'] ?></p>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>