<!doctype html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="<?= public_url() ?>assets/js/alpine.js" defer></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src='<?= public_url() ?>assets/js/mapbox-gl.js'></script>
    <link href='<?= public_url() ?>assets/css/mapbox-gl.css' rel='stylesheet' />
</head>

<body>
    <?php require_once $content; ?>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
</body>

</html>