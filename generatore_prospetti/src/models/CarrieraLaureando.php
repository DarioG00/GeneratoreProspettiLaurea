<?php
require_once '../../src/wrappers/GestioneCarrieraStudente.php';
require_once '../../src/controllers/GestioneFiltroEsami.php';
require_once '../../src/models/Esame.php';

class CarrieraLaureando {
	protected $nome;
    protected $cognome;
    protected $email;
    protected $dataIscrizione;
    protected $corsoDiLaurea;
    protected $matricola;
    protected $dataLaurea;
    protected $dataChiusura;
    protected $esami = [];

    public function __construct($matricola, $cdl, $dataLaurea)
    {
        $this->matricola = $matricola;
        $this->dataLaurea = $dataLaurea;
        $this->corsoDiLaurea = $cdl;

        $gestioneCarrieraStudente = new GestioneCarrieraStudente();
        $carriera = json_decode($gestioneCarrieraStudente->restituisciCarrieraStudente($matricola), true);
        $anagrafica = json_decode($gestioneCarrieraStudente->restituisciAnagraficaStudente($matricola), true);

        $this->nome = $anagrafica['Entries']['Entry']['nome'];
        $this->cognome = $anagrafica['Entries']['Entry']['cognome'];
        $this->email = $anagrafica['Entries']['Entry']['email_ate'];
        $this->dataIscrizione = DateTime::createFromFormat('d/m/Y', $carriera['Esami']['Esame'][0]['INIZIO_CARRIERA']);
        $this->dataChiusura = DateTime::createFromFormat('d/m/Y', $carriera['Esami']['Esame'][0]['DATA_CHIUSURA']);

        for($i = 0; $i < count($carriera['Esami']['Esame']); $i++){

            $esame = $carriera['Esami']['Esame'][$i]['DES'];
            if(is_string($esame)){
                if($this->isInCarriera($esame)){
                    $peso = $carriera['Esami']['Esame'][$i]['PESO'];

                    if($carriera['Esami']['Esame'][$i]['VOTO'] === null){
                        $voto = 0;
                    }else if(strcmp($carriera['Esami']['Esame'][$i]['VOTO'], "30  e lode") == 0){
                        $voto = 33;
                    }else{
                        $voto = intval($carriera['Esami']['Esame'][$i]['VOTO']);
                    }

                    if($carriera['Esami']['Esame'][$i]['SOVRAN_FLG'] === 1){
                        $faMedia = 0;
                    }else if($carriera['Esami']['Esame'][$i]['VOTO'] === null) {
                        $faMedia = 0;
                    }else {
                        $faMedia = 1;
                    }

                    array_push($this->esami, new Esame($esame, $peso, $faMedia, $voto));
                }
            }
        }
    }

    public function getNome()
    {
        return $this->nome;
    }

    public function getCognome()
    {
        return $this->cognome;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getDataIscrizione()
    {
        return $this->dataIscrizione;
    }

    public function getCorsoDiLaurea()
    {
        return $this->corsoDiLaurea;
    }

    public function getDataChiusura()
    {
        return $this->dataChiusura;
    }

    public function getMatricola()
    {
        return $this->matricola;
    }

    public function getDataLaurea()
    {
        return $this->dataLaurea;
    }

    public function getEsame($i)
    {
        return $this->esami[$i];
    }

    public function getNumeroEsami(){
        return count($this->esami);
    }

	public function calcolaMediaPesata() {

        // calcolo della media pesata degli esami
        $mediaPesata = 0.0;
        $sommaPesi = 0;
        for($i = 0; $i < count($this->esami); $i++){
            $peso = $this->esami[$i]->getPeso();
            $faMedia = $this->esami[$i]->getFaMedia();
            $voto = $this->esami[$i]->getVoto();

            if($faMedia){
                $mediaPesata += $peso*$voto;
                $sommaPesi += $peso;
            }
        }

        $mediaPesata = $mediaPesata/$sommaPesi;

        return $mediaPesata;
	}

	public function calcolaCFUTotali() {

        // calcolo della media pesata degli esami informatici
        $cfuTotali = 0;
        for($i = 0; $i < count($this->esami); $i++){
                $peso = $this->esami[$i]->getPeso();
                $cfuTotali += $peso;
        }

        return $cfuTotali;
	}

    public function calcolaCFUTotaliMedia(){
        $sommaPesi = 0;
        for($i = 0; $i < count($this->esami); $i++){
            $peso = $this->esami[$i]->getPeso();
            $faMedia = $this->esami[$i]->getFaMedia();
            if($faMedia){
                $sommaPesi += $peso;
            }
        }
        return $sommaPesi;
    }

    public function isInCarriera($esame){
        $filtroEsami = new GestioneFiltroEsami();
        $esamiNonInMedia = json_decode($filtroEsami->restituisciFiltroEsami(), true);


        for($i = 0; $i < count($esamiNonInMedia['esamiNonInMedia']); $i++){
            if(strcmp($esame, $esamiNonInMedia['esamiNonInMedia'][$i]) == 0){
                return 0;
            }
        }
        return 1;
    }
}
?>