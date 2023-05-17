<?php

require_once __DIR__ . '/BaseRepository.php';
require_once __DIR__ . '/../entities/TipePenumpang.php';

class TipePenumpangRepository extends BaseRepository
{
    protected string $table = "tipe_penumpang";

    public function save(TipePenumpang $tipe): bool
    {
        $bind = [
            'tipe_penumpang' => $tipe->getTipePenumpang()
        ];

        return $this->basicSave($bind);
    }

    public function get(int $length = 10, int $from = 0) : array
    {
        $listData = $this->getDataFromTable($this->table, $length, $from);

        $result = [];
        while ($row = $listData->fetch(PDO::FETCH_ASSOC)) {
            $tipePenumpang = new TipePenumpang();
            $tipePenumpang
                ->setId($row['id'])
                ->setTipePenumpang($row['tipe_penumpang']);

            $result[] = $tipePenumpang;
        }

        return $result;
    }

    public function findById(int $id) : null|TipePenumpang
    {
        $data = $this->basicFindById($id);

        if(!$data) return null;

        return (new TipePenumpang())
            ->setId($data['id'])
            ->setTipePenumpang($data['tipe_penumpang']);
    }

    protected function getTable(): string
    {
        return $this->table;
    }
}
