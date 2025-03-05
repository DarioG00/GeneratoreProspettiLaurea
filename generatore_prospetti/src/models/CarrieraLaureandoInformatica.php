<?php
require_once '../../src/controllers/GestioneEsamiInformatici.php';
require_once '../../src/models/CarrieraLaureando.php';

class CarrieraLaureandoInformatica extends CarrieraLaureando {

    public function getBonus()
    {
        $diff = $this->dataIscrizione->diff($this->dataChiusura);

        if(($diff->y <= 3) && ($diff->m <= 4)){

            // applicazione del bonus
            // calcolo del voto più basso tra gli esami per sfruttare il bonus
            $minV = 33;
            $minP = 12;
            $esameMin = 0;

            for($i = 0; $i < count($this->esami); $i++){
                $peso = $this->esami[$i]->getPeso();
                $voto = $this->esami[$i]->getVoto();

                if(($voto >= 18) && ($voto < $minV)){
                    $esameMin = $i;
                    $minV = $voto;
                    $minP = $peso;
                }else if(($voto == $minV) && ($peso > $minP)){
                    $esameMin = $i;
                    $minP = $peso;
                }
            }

            $this->esami[$esameMin]->setFaMedia(0);

            $bonus = 1;
        }else{
            $bonus = 0;
        }

        return $bonus;
    }

    public function isEsameInformatico($esame){

        // recupero degli esami informatici
        $gestioneEsamiInformatici = new GestioneEsamiInformatici();
        $esamiInformatici = json_decode($gestioneEsamiInformatici->restituisciEsamiInformatici(), true);

        for($i = 0; $i < count($esamiInformatici['esamiIngInf']); $i++){
            if(strcmp($esame, $esamiInformatici['esamiIngInf'][$i]) == 0){
                return 1;
            }
        }
        return 0;
    }

    public function calcolaMediaInformatica() {

        // calcolo della media pesata degli esami informatici
        $mediaInformatica = 0.0;
        $sommaPesi = 0;

        for($i = 0; $i < count($this->esami); $i++){
            $esame = $this->esami[$i]->getNomeEsame();

            if($this->isEsameInformatico($esame)) {
                $peso = $this->esami[$i]->getPeso();
                $faMedia = $this->esami[$i]->getFaMedia();
                $voto = $this->esami[$i]->getVoto();

                if($faMedia){
                    $mediaInformatica += $peso*$voto;
                    $sommaPesi += $peso;
                }
            }
        }

        $mediaInformatica = $mediaInformatica/$sommaPesi;

        return $mediaInformatica;
	}
}
?>