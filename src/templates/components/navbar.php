<!-- component -->
<!-- follow me on twitter @asad_codes -->

<div class="flex flex-wrap place-items-center">
    <section class="relative mx-auto w-full">
        <!-- navbar -->
        <nav class="flex justify-between bg-gray-900 text-white">
            <div class="px-5 xl:px-12 py-6 flex w-full items-center justify-between">
                <a class="text-xl font-bold font-heading" href="<?= site_url() ?>">
                    <!-- <img class="h-9" src="logo.png" alt="logo"> -->
                    PT Sorek Wisata Transport
                </a>
                <!-- Header Icons -->
                <div class="">
                    <div class="block xl:hidden tems-center space-x-5 items-center relative" x-data="{ show_login: false }">
                        <button @click="show_login = !show_login">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 hover:text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        <div x-show="show_login" class="absolute right-0 top-10 z-20 bg-white rounded-lg shadow-lg text-gray-600 w-48 p-4">
                            <!-- Sign In / Register      -->
                            <?php if((session()->auth())) : ?>
                                <a class="flex items-center hover:text-gray-200" href="<?= site_url(session()->auth()->getRole()->redirectPage()) ?>">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 hover:text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="ml-2"><?= session()->auth()->getUsername() ?></span>
                                </a>
                                <a href="<?= site_url('pesan') ?>" class="bg-green-600 text-white px-3 py-1 rounded shandow mt-4">+ Pesan Tiket</a>
                            <?php else: ?>
                                <a class="block hover:text-gray-200" href="<?= site_url('akun/login') ?>">
                                    Login
                                </a>
                                <a class="block hover:text-gray-200" href="<?= site_url('akun/register') ?>">
                                    Daftar Akun
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="hidden lg:block xl:flex items-center space-x-5 items-center">
                        <!-- Sign In / Register      -->
                        <?php if((session()->auth())) : ?>
                            <a class="flex items-center hover:text-gray-200" href="<?= site_url(session()->auth()->getRole()->redirectPage()) ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 hover:text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="ml-2"><?= session()->auth()->getUsername() ?></span>
                            </a>
                            <a href="<?= site_url('pesan') ?>" class="bg-green-600 text-white px-3 py-1 rounded shandow">+ Pesan Tiket</a>
                        <?php else: ?>
                            <a class="flex items-center hover:text-gray-200" href="<?= site_url('akun/login') ?>">
                                Login
                            </a>
                            <a class="flex items-center hover:text-gray-200" href="<?= site_url('akun/register') ?>">
                                Daftar Akun
                            </a>
                        <?php endif; ?>
                        <div class="bg-yellow-500 px-2 py-1 rounded text-white hidden xl:block">
                            Telp: 0812 6828 0330
                        </div>
                    </div>
                </div>
            </div>
        </nav>

    </section>
</div>