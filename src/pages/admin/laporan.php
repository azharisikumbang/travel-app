<?php if (false === session()->isAuthenticatedAs('admin')) html_unauthorized();

$listBulan = [
    'Januari',
    'Februari',
    'Maret',
    'April',
    'Mei',
    'Juni',
    'Juli',
    'Agustus',
    'September',
    'Oktober',
    'November',
    'Desember'
];

?>
<main x-data="container">
    <nav class="block w-full max-w-full bg-transparent text-white shadow-none transition-all px-0 py-1 border-b-2">
        <div class="flex flex-col-reverse justify-between gap-6 md:flex-row md:items-center">
            <h2 class="block antialiased tracking-normal font-sans text-2xl font-semibold leading-relaxed text-gray-900">Data Pelanggan</h2>
            <div class="flex items-center gap-4">
                <span class="font-sans text-gray-500">Sekarang: <?php echo tanggal(date_create()) ?></span>
                <a @click="window.location.reload()" class="underline text-gray-500 hover:text-gray-600 cursor-pointer">Muat Ulang</a>
            </div>
        </div>
    </nav>
    <?php if(session('temp')): ?>
        <div class="mb-4 block w-full text-base font-regular px-4 py-4 rounded-lg bg-green-500 text-white">
            <?php echo session('temp')['message'] ?>
        </div>
    <?php endif; ?>
    <template x-if="properties.messages.errors">
        <template x-for="error in properties.messages.errors">
            <?php html_alert("error.message", "error.color"); ?>
        </template>
    </template>
    <template x-if="properties.messages.normal">
        <template x-for="normal in properties.messages.normal">
            <?php html_alert("normal.message", "normal.color"); ?>
        </template>
    </template>
    <div class="mt-8 px-4 py-8 mb-4 bg-white border border-gray-200 rounded-lg shadow-sm w-full lg:w-1/2">
        <form @submit.prevent="unduhLaporan">
            <div class="mb-4">
                <label for="first-name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Pilih Periode Laporan</label>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <select x-model="properties.form.tahun" class="cursor-pointer shadow-sm bg-gray-50 border-2 border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:border-gray-500 outline-none p-2.5 w-full">
                            <option value="<?= date('Y') ?>">-- Pilih Tahun --</option>
                            <?php for ($i = date('Y'); $i >= 2023; $i--) { ?>
                                <option value="<?= $i ?>"><?= $i ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div>
                        <select x-model="properties.form.bulan" class="cursor-pointer shadow-sm bg-gray-50 border-2 border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:border-gray-500 outline-none p-2.5 w-full">
                            <option value="<?= date('m') ?>">-- Pilih Bulan --</option>
                            <?php for ($i = 1; $i <= 12; $i++) { ?>
                                <option value="<?= $i ?>"><?= $listBulan[$i - 1] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div>
                        <input type="number" x-model="properties.form.tanggal" class="block w-full cursor-pointer shadow-sm bg-gray-50 border-2 border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:border-gray-500 outline-none p-2.5" max="31" min="0" placeholder="*Ketik Tanggal..">
                        <small class="text-sm italic text-red-400">*kosongkan atau ketik 0 jika ingin laporan bulanan.</small>
                    </div>
                </div>
            </div>
            <div>
                <button class="inline-flex text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center" type="submit">
                    <svg class="w-4 h-4 mr-2 text-white" viewBox="0 0 20 20">
                        <path fill="currentColor" d="M14.707 7.793a1 1 0 0 0-1.414 0L11 10.086V1.5a1 1 0 0 0-2 0v8.586L6.707 7.793a1 1 0 1 0-1.414 1.414l4 4a1 1 0 0 0 1.416 0l4-4a1 1 0 0 0-.002-1.414Z"/>
                        <path fill="currentColor" d="M18 12h-2.55l-2.975 2.975a3.5 3.5 0 0 1-4.95 0L4.55 12H2a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-4a2 2 0 0 0-2-2Zm-3 5a1 1 0 1 1 0-2 1 1 0 0 1 0 2Z"/>
                    </svg>
                    Unduh (.pdf])
                </button>
            </div>
        </form>
    </div>
</main>
<script type="text/javascript">
    document.addEventListener('alpine:init', () => {
        const actions = {
            "unduhLaporan": function () {
                if (this.properties.form.tanggal > 31) return;

                if (this.properties.form.tanggal == '') this.properties.form.tanggal = 0;

                window.open(`${this.properties.sites.api_url}/admin/laporan-penjualan?tahun=${this.properties.form.tahun}&bulan=${this.properties.form.bulan}&tanggal=${this.properties.form.tanggal}`, '_blank');
            }
        };

        const utils = {
            "postData": function (to, data, callback, callbackError) {
                let that = this;
                return axios
                    .post(this.properties.sites.api_url + to, data)
                    .then(res => callback(res))
                    .catch(err => callbackError(err));
            },
            "createFormData": function (data) {
                const form = new FormData();
                for (const key in data) form.append(key, data[key]);

                return form;
            },
            "addErrorMassage": function (name, message) {
                this.addMessage(name, message);
            },
            "addNormalMessage": function (name, message) {
                this.addMessage(name, message, 'normal', 'green');
            },
            "addMessage": function (name, message, container = 'errors', color = 'red') {
                let exists = this.properties.messages[container].findIndex(item => item.name == name);

                if (exists != -1) {
                    this.properties.messages[container][exists] = { 'name': name, 'message': message, 'color': color };

                    return;
                }

                this.properties.messages[container].push({ 'name': name, 'message': message, 'color': color });
            },
            "clearMassage": function(){
                this.properties.messages.errors = [];
                this.properties.messages.normal = [];
            }
        };

        Alpine.data('container',
            () => ({
                ...actions,
                ...utils,
                "properties": {
                    "sites": {
                        "api_url": "<?= site_url() ?>"
                    },
                    "messages": {
                        "errors": [],
                        "normal": []
                    },
                    "data": {},
                    "form": {
                        'tahun' : <?= date('Y') ?>,
                        'bulan' : <?= date('m') ?>,
                        'tanggal' : <?= date('d') ?>,
                        'status_pembayaran': '',
                    }
                },
                "init": function() {}
            })
        );
    });
</script>
