<?php
require_once '../../src/models/CarrieraLaureando.php';
require_once '../../src/models/CarrieraLaureandoInformatica.php';

class SimulazioneVotoLaurea {
	private $formula;
    private $tStep;
	private $tMax;
	private $tMin;
	private $cStep;
	private $cMax;
	private $cMin;

    public function __construct($formula, $cMin, $cMax, $cStep, $tMin, $tMax, $tStep){
        $this->formula = $formula;
        $this->cMin = $cMin;
        $this->cMax = $cMax;
        $this->cStep = $cStep;
        $this->tMin = $tMin;
        $this->tMax = $tMax;
        $this->tStep = $tStep;
    }

	public function effettuaSimulazioneVoto($laureando) {
		// Restituisci i voti calcolati nella simulazione voto del laureando
        $voti_sim = [];

        $T = 0;
        $C = 0;
        $M = $laureando->calcolaMediaPesata();
        $CFU = $laureando->calcolaCFUTotaliMedia();
        $formula = str_replace('CFU', 'A', $this->formula);
        $formula = str_replace(['T', 'C', 'M', 'A'], ['$T', '$C', '$M', '$CFU'], $this->formula);

        //echo $formula;

        if($this->tStep === 0){
            // varia solo il voto di commissione
            for($C = $this->cMin; $C <= $this->cMax; $C += $this->cStep){
                $voto = eval("return round($formula, 3);");
                array_push($voti_sim, $voto);
            }
        }else{
            // varia solo il voto di tesi
            for($T = $this->tMin; $T <= $this->tMax; $T += $this->tStep){
                $voto = eval("return round($formula, 3);");
                array_push($voti_sim, $voto);
            }
        }

        return $voti_sim;
	}

    public function getFormula()
    {
        return $this->formula;
    }

    public function getTStep()
    {
        return $this->tStep;
    }

    public function getTMax()
    {
        return $this->tMax;
    }

    public function getTMin()
    {
        return $this->tMin;
    }

    public function getCStep()
    {
        return $this->cStep;
    }

    public function getCMax()
    {
        return $this->cMax;
    }

    public function getCMin()
    {
        return $this->cMin;
    }
}
?>