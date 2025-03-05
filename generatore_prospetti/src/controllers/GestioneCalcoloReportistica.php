<?php

class GestioneCalcoloReportistica {


    public function __construct()
    {
    }

    public function restituisciParametriCorsi() {
        // Ottieni in formato JSON i parametri per il calcolo voto
        $jsonStringCalcoloReportistica = file_get_contents("../../src/controllers/conf/CalcoloReportistica.json");

        return $jsonStringCalcoloReportistica;
	}
}
?>