<?php

require_vendor();
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

require_once __DIR__ . '/../repositories/PemesananRepository.php';

class KelolaLaporan
{
   public function buatExcelLaporanPenjualan(string $kriteria = 'all')
    {
        $pesananRepository = new PemesananRepository();
        $dirtyData = $pesananRepository->getDataForLaporanPenjualan($kriteria);

        $data = [];
        $number = 1;
        /** @var $pesanan Pesanan */
        /** @var $detailPesanan PesananDetail */
        foreach ($dirtyData as $pesanan) {
            $kursi = 1;
            foreach ($pesanan->getListKursi() as $detailPesanan) {
                $tanggalKeberangkatan = date_create(sprintf(
                    "%s %s",
                    $pesanan->getTanggalKeberangkatan()->format('Y-m-d'),
                    $pesanan->getJamKeberangkatan()
                ));

                $data[] = [
                    [ 'type' => 'text', 'value' => $pesanan->getNomorPesanan() ], // No Tiket
                    [ 'type' => 'date', 'value' => $tanggalKeberangkatan->format("Y-m-d H:i:s") ], // Tanggal Keberangkatan
                    [ 'type' => 'text', 'value' => $pesanan->getNamaPemesan() ], // Nama Pemesan
                    [ 'type' => 'text', 'value' => $pesanan->getKategoriPelanggan() ], // Kategori Penumpang
                    [ 'type' => 'number', 'value' => $kursi++  ], // Jumlah Kursi Dipesan
                    [ 'type' => 'text', 'value' => $pesanan->getDriver() ], // Nama Driver
                    [ 'type' => 'text', 'value' => $pesanan->getMobil() ], // Merk Mobil
                    [ 'type' => 'currency', 'value' => rupiah($pesanan->getTotalTarif()) ], // Total Tagihan (Rp)
                    [ 'type' => 'text', 'value' => $pesanan->getTotalTarif() == $pesanan->getTotalDibayarkan() ? 'LUNAS' : 'BAYAR DP' ], // Bayar DP / Lunas
                    [ 'type' => 'currency', 'value' => rupiah($pesanan->getTotalDibayarkan()) ], // Jumlah Dibayar (Rp)
                    [ 'type' => 'currency', 'value' => rupiah($pesanan->getTotalTarif() - $pesanan->getTotalDibayarkan()) ], // Sisa (Rp)
                    [ 'type' => 'currency', 'value' => 0 ], // Jumlah Dikembalikan (50%)
                    [ 'type' => 'text', 'value' => $pesanan->getStatusBuktiPembayaran()->getDisplayName() ], // Status Konfirmasi Pesanan
                    [ 'type' => 'text', 'value' => '' ] // Keterangan
                ];
            }
        }

        $headers = [
            'No Tiket',
            'Tanggal Keberangkatan',
            'Nama Pemesan',
            'Kategori Penumpang',
            'Jumlah Kursi Dipesan',
            'Nama Driver',
            'Merk Mobil',
            'Total Tagihan',
            'Bayar DP / Lunas',
            'Jumlah Dibayar (Rp)',
            'Sisa (Rp)',
            'Jumlah Dikembalikan (50%)',
            'Status Konfirmasi Pesanan',
            'Keterangan'
        ];

        return $this->createFromTemplate($data, $headers);
    }

    /**
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    private function createFromTemplate(
        array $data,
        array $headers
    ): false|Spreadsheet {
        $headerLength = count($headers);
        if (count($data) > 0) {
            $dataColumnLength = count($data[0]); // dengan asumsi banyak data di semua elemen sama (TODO: write better handler)

            if ($dataColumnLength != $headerLength) return false;
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        /* Excel Header */

        $borderStyle = [
            'top' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
            'bottom' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
            'left' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
            'right' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ]
        ];

        $headerStyle = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
            'borders' => $borderStyle,
        ];

        $cellStyle = [
            'borders' => $borderStyle
        ];

        $iterator = $sheet->getColumnIterator();
        $dataItemIndex = 0;
        for($i = 0; $i < $headerLength; $i++) {
            $currentCell = $iterator->current();
            $cellRowIndex = $currentCell->getColumnIndex();
            $cellIndex = sprintf("%s1", $cellRowIndex);
            $sheet->setCellValue($cellIndex, $headers[$i]);
            $sheet->getStyle($cellIndex)->applyFromArray($headerStyle);

            $rowStart = 2;
            for ($dataIndex = 0; $dataIndex < count($data); $dataIndex++) {
                $dataCellIndex = sprintf("%s%s", $cellRowIndex, $rowStart);
                $cell = $sheet->getCell($dataCellIndex);
                $cell->setValue($data[$dataIndex][$dataItemIndex]['value']);
                $cell->getStyle()->applyFromArray($cellStyle);
                switch ($data[$dataIndex][$dataItemIndex]['type']) {
                    case 'number':
                        $cell->getStyle()->getNumberFormat()->setFormatCode('#,##0_-');
                        break;

                    case 'date':
                        $cell->getStyle()->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DATETIME);
                        break;

                    case 'currency':
                        $cell->getStyle()->getNumberFormat()->setFormatCode('Rp #,##0_-');
                        break;
                }

                $rowStart++;
            }


            $iterator->next();
            $dataItemIndex++;
        }

        $cellIterator = $sheet->getRowIterator()->current()->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(true);
        foreach ($cellIterator as $cell) {
            $sheet->getColumnDimension($cell->getColumn())->setAutoSize(true);
        }

        return $spreadsheet;
    }

    /**
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function forceDownloadSpreadsheet(Spreadsheet $spreadsheet, string $filename)
    {
        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. $filename  .'"');
        $writer->save('php://output');
    }
}