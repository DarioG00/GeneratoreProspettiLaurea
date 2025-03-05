<?php
class Esame {
	private $voto;
	private $peso;
	private $nome;
    private $faMedia;

    public function __construct($nome, $peso, $faMedia, $voto){
        $this->voto = $voto;
        $this->peso = $peso;
        $this->nome = $nome;
        $this->faMedia = $faMedia;
    }

    public function getNomeEsame()
    {
        return $this->nome;
    }

    public function getVoto()
    {
        return $this->voto;
    }

    public function getPeso()
    {
        return $this->peso;
    }

    public function getFaMedia()
    {
        return $this->faMedia;
    }

    public function setFaMedia($faMedia)
    {
        $this->faMedia = $faMedia;
    }
}
?>