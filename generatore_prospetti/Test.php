<?php
require_once "../../src/models/CarrieraLaureando.php";
require_once "../../src/models/CarrieraLaureandoInformatica.php";
require_once "../../src/models/SimulazioneVotoLaurea.php";
require_once "../../src/controllers/GestioneCalcoloReportistica.php";

class Test{

    public function __construct()
    {
    }

    public function eseguiTest(){
        $file_test = file_get_contents("../casi_test.json");
        $laureandi = json_decode($file_test, true);

        // per ogni laureando
        for($i = 0; $i < count($laureandi['laureandi']); $i++){

            $matricola = $laureandi['laureandi'][$i]['laureando']['matricola'];
            $cdl = $laureandi['laureandi'][$i]['laureando']['corsoDiLaurea'];
            $dataLaurea = $laureandi['laureandi'][$i]['laureando']['dataLaurea'];
            echo "<div>Test matricola: '" . $matricola . "', corso di laurea: '" . $cdl . "', data di laurea: '" . $dataLaurea . "'</div>";

            if(strcmp($cdl, "T. Ing. Informatica") == 0){
                $laureando = new CarrieraLaureandoInformatica($matricola, $cdl, $dataLaurea);

                // test bonus informatica
                $bonus = $laureandi['laureandi'][$i]['laureando']['bonusInf'];
                if($laureando->getBonus()){
                    if(strcmp($bonus, "SI") == 0){
                        $esito = 'OK';
                    }else{
                            $esito = 'ERRORE';
                    }
                }else{
                    if(strcmp($bonus, "NO") == 0){
                        $esito = 'OK';
                    }else{
                        $esito = 'ERRORE';
                    }
                }

                echo "<div>Test bonus informatica: " . $esito . "</div>";

                // test media pesata informatica
                $mediaPesataInf = $laureandi['laureandi'][$i]['laureando']['mediaPesataInf'];
                if($mediaPesataInf == round($laureando->calcolaMediaInformatica(), 3)){
                    $esito = 'OK';
                }else{
                    $esito = 'ERRORE';
                }
                echo "<div>Test media pesata informatica: " . $esito . "</div>";

            }else{
                $laureando = new CarrieraLaureando($matricola, $cdl, $dataLaurea);
            }

            // test media pesata
            $mediaPesata = $laureandi['laureandi'][$i]['laureando']['mediaPesata'];
            if($mediaPesata == round($laureando->calcolaMediaPesata(), 3)){
                $esito = 'OK';
            }else{
                $esito = 'ERRORE';
            }
            echo "<div>Test media pesata: " . $esito . "</div>";

            // test cfu che fanno media
            $cfuMedia = $laureandi['laureandi'][$i]['laureando']['cfuMedia'];
            if($cfuMedia == round($laureando->calcolaCFUTotaliMedia(), 3)){
                $esito = 'OK';
            }else{
                $esito = 'ERRORE';
            }
            echo "<div>Test cfu che fanno media: " . $esito . "</div>";

            // test dei voti simulazione
            $voti = $laureandi['laureandi'][$i]['laureando']['voti'];

            $gestioneCalcoloReportistica = new GestioneCalcoloReportistica();
            $parametri = json_decode($gestioneCalcoloReportistica->restituisciParametriCorsi(), true);

            $formula = $parametri['corsi'][$cdl]['formula_voto_string'];
            $cMin = $parametri['corsi'][$cdl]['Cmin'];
            $cMax = $parametri['corsi'][$cdl]['Cmax'];
            $cStep = $parametri['corsi'][$cdl]['Cstep'];
            $tMin = $parametri['corsi'][$cdl]['Tmin'];
            $tMax = $parametri['corsi'][$cdl]['Tmax'];
            $tStep = $parametri['corsi'][$cdl]['Tstep'];
            $simulazione = new SimulazioneVotoLaurea($formula, $cMin, $cMax, $cStep, $tMin, $tMax, $tStep);

            $votiCalcolati = $simulazione->effettuaSimulazioneVoto($laureando);

            $esito = 'OK';
            for($j = 0; $j < count($votiCalcolati); $j++){
                if($voti[$j] != $votiCalcolati[$j]){
                    $esito = 'ERRORE';
                }
            }
            echo "<div>Test voti simulazione: " . $esito . "</div>";

            echo "<hr>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="it-IT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generatore Prospetti di Laurea: Test</title>
</head>
<body>
<div>
    <h1>Generatore Prospetti di Laurea - Test</h1>
    <pre><?php
        $t = new Test();
        $t->eseguiTest();
        ?></pre>
</div>
</body>
</html>

