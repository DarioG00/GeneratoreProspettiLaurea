<?php
class GestioneEsamiInformatici {

    public function __construct()
    {
    }

    public function restituisciEsamiInformatici() {
		$jsonStringEsamiInf = file_get_contents('../../src/controllers/conf/EsamiInformatici.json');
        return $jsonStringEsamiInf;
	}
}
?>