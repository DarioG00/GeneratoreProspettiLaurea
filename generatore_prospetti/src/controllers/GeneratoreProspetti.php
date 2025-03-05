<?php
require_once '../../src/controllers/GenerazioneProspetti.php';
require_once '../../src/controllers/InvioProspetti.php';
require_once '../../src/controllers/AccessoProspetti.php';

class GeneratoreProspetti {

    public function __construct()
    {
    }

    public function richiestaInvioProspetti($matricole, $cdl, $dataLaurea) {

        // invio prospetti a tutte le matricole
        $mailer = new InvioProspetti();
        $num_inviati = $mailer->inviaProspetti($matricole, $cdl, $dataLaurea);

        return $num_inviati;
	}

	public function richiestaGenerazioneProspetti($matricole, $cdl, $dataLaurea) {

		// generazione dei prospetti per i laureandi e per la commissione
        $dataLaurea = new DateTime($dataLaurea);
        $generatore = new GenerazioneProspetti();
        $generatore->generaProspettiPDF($matricole, $cdl, $dataLaurea);
	}

	public function richiestaAccessoProspetti() {

		// accesso al prospetto per la commissione
        $controlloreAccesso = new AccessoProspetti();
        $controlloreAccesso->accediProspetti();
	}
}

if($_SERVER["REQUEST_METHOD"] == "POST"){

    session_start();

    // gestione richiesta utente
    $matricole = preg_split("/\s+/", $_POST['matricole']);
    $intGeneratoreProspetti = new GeneratoreProspetti();

    $_SESSION['num_matricole'] = count($matricole);

    if(isset($_POST['generazione'])){

        unset($_POST['generazione']);

        $intGeneratoreProspetti->richiestaGenerazioneProspetti($matricole, $_POST['cdl'], $_POST['dataLaurea']);

        $_SESSION['generati'] = 1;

        header('Location: ../../index.php');

    }else if(isset($_POST['invio'])){

        unset($_POST['invio']);

        $_SESSION['inviati'] = $intGeneratoreProspetti->richiestaInvioProspetti($matricole, $_POST['cdl'], $_POST['dataLaurea']);

        header('Location: ../../index.php');

    }else if(isset($_POST['accesso'])){

        unset($_POST['accesso']);

        $intGeneratoreProspetti->richiestaAccessoProspetti();
    }

    exit();

}else{
    header("Location: ../index.php");
    exit();
}
?>