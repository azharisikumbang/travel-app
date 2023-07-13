<?php

final class PenyimpananService
{
    private array $disk;
    public function __construct()
    {
        $this->disk = app()->getConfigFrom('app', 'penyimpanan');
    }

    public function simpanBuktiPembayaran(array $file) : false|string
    {
        $path = $file['name'];
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        $filename = strtolower(md5($file['name'] . microtime()) . "." . $ext);
        $target = sprintf("%s/%s", rtrim($this->disk['bukti_pembayaran'], "/"), basename($filename));

        if(false === move_uploaded_file($file['tmp_name'], $target)) return false;

        return $filename;
    }

    public function downloadBuktiPembayaran(string $file): void
    {
        $filename = sprintf("%s/%s", rtrim($this->disk['bukti_pembayaran'], "/"), basename($file));

        if (file_exists($filename)) {
            header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
            header("Cache-Control: public"); // needed for internet explorer
            header("Content-Type: application/image");
            header("Content-Transfer-Encoding: Binary");
            header("Content-Length:".filesize($filename));
            header("Content-Disposition: attachment; filename=".$file);
            readfile($filename);
            die();
        }
    }

    public function getDisk() : array
    {
        return $this->disk;
    }

    public function simpanFileTiketPDF(string $filename, ?string $content) : string
    {
        $file = sprintf("%s/%s", rtrim($this->disk['tiket'], "/"), $filename);
        file_put_contents($file, $content);

        return $filename;
    }

    public function downloadTiket(string $file)
    {
        $filename = sprintf("%s/%s", rtrim($this->disk['tiket'], "/"), basename($file));

        if (file_exists($filename)) {
            header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
            header("Cache-Control: public"); // needed for internet explorer
            header("Content-Type: application/image");
            header("Content-Transfer-Encoding: Binary");
            header("Content-Length:".filesize($filename));
            header("Content-Disposition: attachment; filename=".$file);
            readfile($filename);
            die();
        }
    }

    public function simpanPhotoIdentitasPelanggan(array $file) : false|string
    {
        $path = $file['name'];
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        $filename = strtolower(md5($file['name'] . microtime()) . "." . $ext);
        $target = sprintf("%s/%s", rtrim($this->disk['photo_identitas'], "/"), basename($filename));

        if(false === move_uploaded_file($file['tmp_name'], $target)) return false;

        return $filename;
    }

    public function downloadPhotoIdentitas(string $file)
    {
        $filename = sprintf("%s/%s", rtrim($this->disk['photo_identitas'], "/"), basename($file));

        if (file_exists($filename)) {
            header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
            header("Cache-Control: public"); // needed for internet explorer
            header('Content-Type: image/jpg');
            header("Content-Length:".filesize($filename));
            header('Expires: 0');
            header('Pragma: public');
            header("Content-Disposition: attachment; filename=".$file);
            file_get_contents($filename);
            die();
        }
    }

    public function getFullPath(string $disk, $file) : string
    {
        return sprintf("%s/%s", rtrim($this->disk[$disk], "/"), basename($file));
    }
}