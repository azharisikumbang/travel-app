<?php

/** @var $app App */

//var_dump($app->getConfigFrom('app', 'site_url')); die;

if(!function_exists('app')) {
    function app(): App
    {
        if (!isset($app)) {
            global $app;

            if (is_null($app)) $app = new App();
        }

        return $app;
    }
}

if(!function_exists('config')) {
    function config(string $container, string $name) : mixed
    {
        $app = app();

        return $app->getConfigFrom($container, $name);
    }
}

if (!function_exists('base_path')) {
    function base_path() : string
    {
        return __DIR__ . "/../";
    }
}

if (!function_exists('site_url')) {
    function site_url(string $url = "") : string
    {
        $siteUrl = rtrim(config('app', 'site_url'), "/");

        return $url ? sprintf("%s/%s", $siteUrl, $url) : $siteUrl;
    }
}

if (!function_exists('public_url')) {
    function public_url() : string
    {
        return config('app', 'site_url');
    }
}

if (!function_exists('session')) {
    function session(string $key) : mixed
    {
        $manager = app()->getManager()->getSessionManager();

        return $manager->exists($key) ? $manager->get($key) : null;
    }
}

if (!function_exists('tanggal')) {
    function tanggal(DateTimeInterface $date, bool $month = true, bool $full = false)
    {
        $format = $full ? "Y/m/d H:i:s" : "Y/m/d";
        $today = $date->format($format);
        if ($month) {
            $listMonth = [
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

            $exploded = explode("/", $today);

            return sprintf("%s %s %s", $exploded[2], $listMonth[$exploded[1] - 1], $exploded[0]);
        }

        return $today;
    }
}

if(!(function_exists('rupiah'))) {
    function rupiah(float $number, string $tanda = ".")
    {
        return number_format($number, 0, ",", $tanda);
    }
}