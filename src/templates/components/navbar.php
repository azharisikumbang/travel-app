<!-- component -->
<!-- follow me on twitter @asad_codes -->

<div class="flex flex-wrap place-items-center">
    <section class="relative mx-auto">
        <!-- navbar -->
        <nav class="flex justify-between bg-gray-900 text-white w-screen">
            <div class="px-5 xl:px-12 py-6 flex w-full items-center">
                <a class="text-xl font-bold font-heading" href="<?= site_url() ?>">
                    <!-- <img class="h-9" src="logo.png" alt="logo"> -->
                    PT Sorek Wisata Transport
                </a>
                <!-- Nav Links -->
                <ul class="hidden md:flex px-4 mx-auto font-semibold font-heading space-x-12">
<!--                    <li><a class="hover:text-gray-200" href="#">Home</a></li>-->
<!--                    <li><a class="hover:text-gray-200" href="#">Catagory</a></li>-->
<!--                    <li><a class="hover:text-gray-200" href="#">Collections</a></li>-->
<!--                    <li><a class="hover:text-gray-200" href="#">Contact Us</a></li>-->
                </ul>
                <!-- Header Icons -->
                <div class="hidden xl:flex items-center space-x-5 items-center">
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
                </div>
            </div>
        </nav>

    </section>
</div>