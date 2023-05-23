<?php

echo json_encode(
    app()
        ->getManager()
        ->getService('TarifService')
        ->lihatDetailTarif($_GET['tarif'])
        ->toArray()
);

