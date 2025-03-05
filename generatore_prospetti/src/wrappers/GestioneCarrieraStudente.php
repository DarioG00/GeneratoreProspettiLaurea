<?php

class GestioneCarrieraStudente {

    public function __construct(){
    }

    public function restituisciCarrieraStudente($matricola) {
		// Ottieni in formato JSON la carriera dello studente e restituiscila come stringa
        $path = "../../src/wrappers/data/{$matricola}_esami.json";
        $jsonStringCarriera = file_get_contents($path);

        return $jsonStringCarriera;
	}

	public function restituisciAnagraficaStudente($matricola) {
        // Ottieni in formato JSON l'anagrafica dello studente e restituiscila come stringa
        $path = "../../src/wrappers/data/{$matricola}_anagrafica.json";
        $jsonStringAnagrafica = file_get_contents($path);

        return $jsonStringAnagrafica;
	}
}
?>