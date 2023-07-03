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

if(!(function_exists('get_current_path'))) {
    function get_current_route(bool $full = false) : string
    {
        $router = app()->getManager()->getRouterManager();

        return $full ? site_url($router->getPath()) : rtrim($router->getPath(), "/");
    }
}

if (!function_exists('session')) {
    function session(string $key = null) : mixed
    {
        $manager = app()->getManager()->getSessionManager();

        if (is_null($key)) return $manager;

        return $manager->exists($key) ? $manager->get($key) : null;
    }
}

if (!function_exists('response')) {
    function response()
    {
        return app()->getManager()->getResponseManager();
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

if(!(function_exists('html_active_menu'))) {
    function html_active_menu(string $route) : string
    {
        return (strtolower(get_current_route()) == $route)
            ? 'middle none font-sans font-bold center transition-all disabled:opacity-50 disabled:shadow-none disabled:pointer-events-none text-xs py-3 rounded-lg bg-gradient-to-tr from-blue-600 to-blue-400 text-white shadow-md shadow-blue-500/20 hover:shadow-lg hover:shadow-blue-500/40 active:opacity-[0.85] w-full flex items-center gap-4 px-4 capitalize'
            : 'middle none font-sans font-bold center transition-all disabled:opacity-50 disabled:shadow-none disabled:pointer-events-none text-xs py-3 rounded-lg text-white hover:bg-white/10 active:bg-white/30 w-full flex items-center gap-4 px-4 capitalize';
    }
}

if(!(function_exists('html_require_component'))) {
    function html_require_component(string $name, mixed $data = null) : void
    {
        require_once __DIR__ . '/templates/components/' . $name . '.php';
    }
}


if(!(function_exists('html_alert'))) {
    function html_alert(string $message, string $color = 'red') : void
    {
        require __DIR__ . '/templates/components/alert.php';
    }
}

if(!(function_exists('html_temp_alert'))) {
    function html_temp_alert(string $message, string $color = 'red') : void
    {
        require __DIR__ . '/templates/components/temp_alert.php';
    }
}

if(!(function_exists('html_not_found'))) {
    function html_not_found(string $message = 'Tidak Ditemukan.') : void
    {
        require __DIR__ . '/pages/static/404.php';
    }
}