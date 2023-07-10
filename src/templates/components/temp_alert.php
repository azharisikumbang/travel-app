<div x-data="{ open: true }" >
    <div role="alert" class="bg-<?php echo $color ?>-400 relative block mb-4 w-full text-base font-regular px-4 py-4 rounded-lg text-white" style="opacity: 1;" x-show="open">
        <div class="mr-12"><?php echo $message ?></div>
        <div class="absolute top-3 right-3 w-max rounded-lg hover:bg-white hover:bg-opacity-20 transition-all">
            <div role="button" class="w-max p-1 rounded-lg" @click="open = ! open">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
            </div>
        </div>
    </div>
</div>