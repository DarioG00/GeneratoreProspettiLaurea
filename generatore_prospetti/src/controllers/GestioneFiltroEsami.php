<?php
class GestioneFiltroEsami {

    public function __construct()
    {
    }

    public function restituisciFiltroEsami() {
        $jsonStringFiltroEsami = file_get_contents('../../src/controllers/conf/FiltroEsami.json');
        return $jsonStringFiltroEsami;
    }
}
?>