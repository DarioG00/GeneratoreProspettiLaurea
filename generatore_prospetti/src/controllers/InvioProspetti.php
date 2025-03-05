<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once '../../lib/PHPMailer/src/PHPMailer.php';
require '../../lib/PHPMailer/src/Exception.php';
require '../../lib/PHPMailer/src/SMTP.php';
require_once '../../src/controllers/GestioneCalcoloReportistica.php';

class InvioProspetti {

    public function __construct()
    {
    }

    public function inviaProspetti($matricole, $cdl, $dataLaurea) {

        // Invio prospetti a tutte i laureandi
        $num_inviati = 0;
        $gestioneCalcoloReportistica = new GestioneCalcoloReportistica();
        $parametri = json_decode($gestioneCalcoloReportistica->restituisciParametriCorsi(), true);
        for($i = 0; $i < count($matricole); $i++){
            $laureando = new CarrieraLaureando($matricole[$i], $cdl, $dataLaurea);
            $email_body = $parametri["corsi"][$cdl]["email_body"];

            $email = new PHPMailer();
            $email->IsSMTP();
            $email->Host = "mixer.unipi.it";
            $email->SMTPSecure = "tls";
            $email->Port = 25;
            $email->SMTPAuth = false;

            $email->addCustomHeader('Content-Type', 'text/plain; windows-1252');
            $email->setFrom("no-reply-laureandosi@ing.unipi.it", "Laureandosi 2.0");
            $email->AddAddress($laureando->getEmail());
            $email->AddAttachment("../../src/views/prospettiPDF/{$matricole[$i]}_pdf_laureando.pdf");

            $email->Subject = "Appello di laurea in ". $cdl . "- indicatori per voto di laurea";
            $email->Body = stripslashes($email_body);
            if($email->send()){
                $num_inviati++;
            }

            $email->SmtpClose();
            unset($email);
        }

        return $num_inviati;
	}

}
?>