<?php html_require_component('navbar'); ?>
<main>
    <div class="max-w-2xl mx-auto py-40 px-6">
        <?php if(session('temp')):
            html_temp_alert(session('temp')['message'], 'yellow');
        endif; ?>
        <form action="<?= site_url('otentikasi') ?>" method="post">
            <div class="bg-gray-100 rounded-lg px-6 py-12">
                <h2 class="block antialiased tracking-normal font-sans text-xl font-bold leading-relaxed text-gray-900 text-center mb-8">Masuk Ke Akun</h2>
                <div class="w-full mb-4">
                    <label for="" class="font-sans text-base text-gray-600 mb-2 block">Username</label>
                    <input name="username" type="text" class="w-full bg-white text-gray-700 font-sans font-normal outline outline-0 border-2 text-sm px-3 py-3 rounded-md border-gray-200 focus:border-gray-400" autofocus>
                </div>
                <div class="w-full mb-4">
                    <label for="" class="font-sans text-base text-gray-600 mb-2 block">Password</label>
                    <input name="password" type="password" class="w-full bg-white text-gray-700 font-sans font-normal outline outline-0 border-2 text-sm px-3 py-3 rounded-md border-gray-200 focus:border-gray-400">
                </div>
                <div class="w-full mt-8">
                    <button type="submit" class="w-full text-white bg-gray-700 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-4 py-4 text-center mr-3 md:mr-0">Masuk</button>
                </div>
            </div>
        </form>
    </div>
</main>
