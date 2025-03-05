<?php
require_once '../../src/views/ProspettoPDFLaureando.php';
require_once '../../src/views/ProspettoPDFInformatica.php';
require_once '../../src/views/ProspettoPDFCommissione.php';

class GenerazioneProspetti {

    public function __construct()
    {
    }

    public function generaProspettiPDF($matricole, $cdl, $dataLaurea) {

        $laureandi = [];
        $n = count($matricole);

        // generazione prospetti per i laureandi
        for($i=0; $i < $n; $i++){
            if(strcmp($cdl, "T. Ing. Informatica") == 0){
                $laureando = new CarrieraLaureandoInformatica($matricole[$i], $cdl, $dataLaurea);
                $vistaPdf = new ProspettoPDFInformatica();
            }else{
                $laureando = new CarrieraLaureando($matricole[$i], $cdl, $dataLaurea);
                $vistaPdf = new ProspettoPDFLaureando();
            }

            array_push($laureandi, $laureando);

            $vistaPdf->generaPDF($laureando);
            $vistaPdf->generaPDFConSimulazione($laureando);
        }

        // generazione prospetti per la commissione
        $vistaPdfCommissione = new ProspettoPDFCommissione();
        $vistaPdfCommissione->generaPDF($laureandi);
	}
}
?>