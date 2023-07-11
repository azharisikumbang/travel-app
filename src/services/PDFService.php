<?php

require_once __DIR__ . '/PenyimpananService.php';
load_externals(function ($externalDir) {
    require_once $externalDir . 'dompdf/autoload.inc.php';
});

use Dompdf\Dompdf;

class PDFService
{
    private PenyimpananService $penyimpananService;

    public function __construct(PenyimpananService $penyimpananService)
    {
       $this->penyimpananService = $penyimpananService;
    }

    public function buatTiket(Pesanan $pesanan) : string
    {
        // load template
        $disk = $this->penyimpananService->getDisk();
        require_once $disk['tiket_templates'];

        $content = ob_get_clean();
        $dompdf = new Dompdf();
        $dompdf->loadHtml($content);
        $dompdf->setPaper('A5', 'landscape');
        $dompdf->render();
        $output = $dompdf->output();

        $nomorPesanan = str_replace("/",  "_", $pesanan->getNomorPesanan());
        $filename = strtolower($nomorPesanan. '-' . md5($pesanan->getNomorPesanan() . microtime()) . ".pdf");

        return $this->penyimpananService->simpanFileTiketPDF($filename, $output);
    }
}