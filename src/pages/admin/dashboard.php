<nav class="block w-full max-w-full bg-transparent text-white shadow-none transition-all px-0 py-1 border-b-2">
    <div class="flex flex-col-reverse justify-between gap-6 md:flex-row md:items-center">
        <h2 class="block antialiased tracking-normal font-sans text-2xl font-semibold leading-relaxed text-gray-900">Dashboard</h2>
        <div class="flex items-center">
            <span class="font-sans text-gray-500"><?php echo tanggal(date_create()) ?></span>
        </div>
    </div>
</nav>
<div id="content" class="mt-8 w-full overflow-hidden">
    <?php echo config('xxx') ?>
</div>