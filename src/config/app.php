<?php

return [
    'site_title' => 'PT. Sorek Wisata Transport',
    'base_dir' => __DIR__ . '/../../',
    'site_url' => 'http://localhost/travel-app/public/',
    'base_url' => 'http://localhost/travel-app/',
    'penyimpanan' => [
        'base_dir' => __DIR__ . '/../storages/',
        'bukti_pembayaran' => __DIR__ . '/../storages/bukti-pembayaran',
        'tiket' => __DIR__ . '/../storages/tiket',
        'tiket_templates' => __DIR__ . '/../templates/tiket.php'
    ],
    'kontak' => [
        'utama' => '081268280330',
        'cabang'=> [
            [
                'kota' => 'Sorek',
                'nomor' => ['081398885884', '081323424200']
            ],[
                'kota' => 'Pangkalan Kerinci',
                'nomor' => ['081398885884', '081323424100']
            ],[
                'kota' => 'Padang',
                'nomor' => ['082268885884', '081323424100']
            ]
        ]
    ]
];