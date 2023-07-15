<?php

if (false === session()->isAuthenticatedAs('driver')) html_unauthorized();
$akun = session()->auth();

?>
<main>
    <nav class="block w-full max-w-full bg-transparent text-white shadow-none transition-all px-0 py-1 border-b-2">
        <div class="flex flex-col-reverse justify-between gap-6 md:flex-row md:items-center">
            <h2 class="block antialiased tracking-normal font-sans text-2xl font-semibold leading-relaxed text-gray-900">Pengaturan Akun</h2>
        </div>
    </nav>
    <div id="content" class="mt-8 w-full overflow-hidden">
        <div class="mb-4 sm:w-4/12">
            <?php if(session('temp')): ?>
                <div class="mb-4 block w-full text-base font-regular px-4 py-4 rounded-lg bg-<?= session('temp')['status'] ? 'green' : 'yellow'  ?>-500 text-white">
                    <?php echo session('temp')['message'] ?>
                </div>
            <?php endif; ?>
            <form action="<?= site_url('driver/pengaturan/kata-sandi/simpan'); ?>" method="post">
                <div class="w-full min-w-[200px] mb-4">
                    <label for="" class="font-sans text-base text-gray-500 mb-2 block">Password</label>
                    <input type="password" name="password" class="w-full bg-white text-gray-700 font-sans font-normal outline outline-0 border-2 text-sm px-3 py-3 rounded-md border-gray-200 focus:border-gray-400">
                </div>
                <div class="w-full min-w-[200px] mb-4">
                    <label for="" class="font-sans text-base text-gray-500 mb-2 block">Ketik Ulang Password</label>
                    <input type="password" name="password_confirmation" class="w-full bg-white text-gray-700 font-sans font-normal outline outline-0 border-2 text-sm px-3 py-3 rounded-md border-gray-200 focus:border-gray-400">
                </div>
                <div class="w-full min-w-[200px] mt-8 flex justify-end">
                    <button type="submit" class="bg-gray-700 text-white rounded py-2 px-8 font-sans center">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</main>
