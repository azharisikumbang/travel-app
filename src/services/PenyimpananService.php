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
}