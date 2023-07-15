<?php

if (false === session()->isAuthenticatedAs('driver')) html_unauthorized();
$auth = session()->auth();

?><!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
</head>
<body>
<?php html_require_component('navbar'); ?>
<section class="max-w-screen-2xl mx-auto py-2 sm:py-20 flex flex-wrap sm:flex-col">
    <aside class='w-full p-2 sm:w-3/12 order-2 sm:order-first'>
        <div class="border rounded-lg p-4 shadow-md">
            <div class="flex border-b-2 pb-4 items-center">
                <div class="w-20 mr-4">
                    <img src="https://ui-avatars.com/api/?name=<?= $auth->getUsername() ?>" alt="<?= $auth->getUsername() ?>" class="rounded-full">
                </div>
                <div class="w-full">
                    <h2 class="font-sans text-xl font-semibold text-gray-800"><?= $auth->getUsername() ?></h2>
                    <p class="text-sm font-light font-sans"><?= $auth->getRole()->value ?></p>
                </div>
            </div>
            <ul class="flex flex-col gap-1 mt-4">
                <li class="mb-1">
                    <a href="<?= site_url('driver/jadwal') ?>" class="font-sans text-gray-800 hover:text-opacity-75 hover:underline">Lihat Jadwal Harian</a>
                </li>
                <li class="mb-1">
                    <a href="<?= site_url('driver/pengaturan/kata-sandi') ?>" class="font-sans text-gray-800 hover:text-opacity-75 hover:underline">Ganti Kata Sandi</a>
                </li>
                <li class="mb-1">
                    <a href="<?= site_url('akun/logout') ?>" class="font-sans text-gray-800 hover:text-opacity-75 hover:underline">Akhiri Sesi</a>
                </li>
            </ul>
            <div class="my-4">
                <p class="text-gray-600">&copy; PT. Sorek Wisata Transport</p>
            </div>
        </div>
    </aside>
    <main class="w-full order-1 px-2 mb-12 sm:px-8 sm:w-9/12 sm:order-last">
        <?php require_once $content; ?>
    </main>
</section>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
</body>
</html>