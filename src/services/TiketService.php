<?php

require_once __DIR__ . '/../entities/KategoriPelanggan.php';
require_once __DIR__ . '/../entities/DaerahOperasional.php';
require_once __DIR__ . '/../entities/Tiket.php';
require_once __DIR__ . '/../entities/Rute.php';
require_once __DIR__ . '/../repositories/DaerahOperasionalRepository.php';
require_once __DIR__ . '/../repositories/KategoriPelangganRepository.php';
require_once __DIR__ . '/../repositories/TiketRepository.php';
require_once __DIR__ . '/../repositories/RuteRepository.php';

class TiketService
{
    private RuteRepository $ruteRepository;

    private KategoriPelangganRepository $kategoriPelangganRepository;

    private TiketRepository $tiketRepository;

    public function __construct()
    {
        $this->kategoriPelangganRepository = new KategoriPelangganRepository();
        $this->tiketRepository = new TiketRepository();
        $this->ruteRepository = new RuteRepository();
    }

    public function listTarif(int $length = 10, int $from = 0)
    {
        return $this->tiketRepository->get($length, $from);
    }

    public function detailTiket(int $id): null|Tiket
    {
        if ($id < 1) return null;

        return $this->tiketRepository->findById($id);
    }

    public function cekTiketTersedia(Rute $rute, int $kategoriPelanggan): false|Tiket
    {
        $kategoriPelanggan = $this->kategoriPelangganRepository->findById($kategoriPelanggan);
        if (is_null($kategoriPelanggan)) return false;

        $tiket = $this->tiketRepository->getByRuteAndKategori($rute, $kategoriPelanggan);

        return $tiket ?: false;
    }

    public function listTarifByKategori(): array
    {
        $listTiket = $this->tiketRepository->get(100, 0);

        $result = [];
        /** @var $tiket Tiket */
        foreach ($listTiket as $tiket) {
            if (!isset($result[$tiket->getKategoriPelanggan()->getId()])) $result[$tiket->getKategoriPelanggan()->getId()] = [];

            $result[$tiket->getKategoriPelanggan()->getId()][] = [
                'kategori_id'  => $tiket->getKategoriPelanggan()->getId(),
                'nama' => $tiket->getKategoriPelanggan()->getKategori(),
                'tiket' => $tiket->toArray()
            ];
        }

        return array_values($result);
    }

    public function simpan(int $id, int $rute, int $kategori, int|float $tarif) : false|Tiket
    {
        if ($tarif < 0) return false;

        $rute = $this->ruteRepository->findById($rute);
        if (is_null($rute)) return false;

        $kategori = $this->kategoriPelangganRepository->findById($kategori);
        if (is_null($kategori)) return false;

        $entity = new Tiket();
        $entity->setId($id);
        $entity->setRute($rute);
        $entity->setKategoriPelanggan($kategori);
        $entity->setTarif($tarif);

        return $this->tiketRepository->updateOrCreate($entity);
    }

    public function hapus(int|Tiket $tiket) : bool
    {
        return $this->tiketRepository->deleteById($tiket);
    }
}
