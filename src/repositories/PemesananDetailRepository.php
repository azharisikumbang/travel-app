<?php

require_once __DIR__ . '/BaseRepository.php';
require_once __DIR__ . '/../entities/PesananDetail.php';
require_once __DIR__ . '/../entities/Pesanan.php';

class PemesananDetailRepository extends BaseRepository
{
    private string $table = "pesanan_detail";
    protected function getTable(): string
    {
        return $this->table;
    }

    public function saveMany(Pesanan $pesanan) : void
    {
        foreach ($pesanan->getListKursi() as $item) {
            if(!($item instanceof PesananDetail)) continue;

            $this->basicSave([
                'pesanan_id' => $pesanan->getId(),
                'nomor_kursi' => $item->getNomorKursi(),
                'harga_tiket' => $item->getHargaTiket()
            ]);
        }
    }

}
