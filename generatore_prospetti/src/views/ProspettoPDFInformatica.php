<?php
require_once '../../src/controllers/GestioneCalcoloReportistica.php';
require_once '../../src/controllers/GestioneEsamiInformatici.php';
require_once '../../src/models/CarrieraLaureandoInformatica.php';
require_once '../../src/models/SimulazioneVotoLaurea.php';
require_once '../../src/models/Esame.php';
require_once '../../lib/Mpdf/vendor/autoload.php';

class ProspettoPDFInformatica {

    public function __construct()
    {
    }

    public function creaPaginaLaureando($laureando, $formula, $cfuRichiesti){
        $cdl = $laureando->getCorsoDiLaurea();
        $matricola = $laureando->getMatricola();

        // Applico il bonus
        $bonus = 'NO';
        if($laureando->getBonus()){
            $bonus = 'SI';
        }

        $html = '
                <style>
                    body { font-family: Arial, sans-serif; margin: 0em; padding: 0em;}
                    
                    .intestazione { text-align: center; font-size: 16px; margin: 0em; padding: 0em; }
                    
                    .info-laureando, .info-calcoli { width: 100%; font-size: 14px; text-align: left; border: 1px solid black; border-collapse: collapse; margin-bottom: 8px; padding: 0em; }
                    .info-laureando td { padding: 3px; padding-top: 1px; padding-bottom: 1px;}
                    
                    .esami { width: 100%; border: 1px solid black; border-collapse: collapse; margin-bottom: 0.5em; margin-top: 0em; padding: 0em; }
                    .esami td { text-align: center; font-size: 12px; border: 1px solid black; margin: 0em; padding: 0em;}
                    .esami .myTh { text-align: center; font-size: 14px; padding: 2px;}
                    .esami .esame { text-align: left; padding: 0em;}
                    
                    .simulazione { width: 100%; border: 1px solid black; border-collapse: collapse; margin-bottom: 0.5em; margin-top: 0em; padding: 0em; }
                    .simulazione td { text-align: center; font-size: 12px; padding: 2px; border: 1px solid black; }
                    .simulazione .myTH { font-size: 14px; width: 50%; }
                    .info-voto-finale { text-align: left; font-size: 14px; margin: 1em; padding: 0em; }
                </style>
                
                <div class="intestazione">
                    <div>' . $cdl . '</div>
                    <div>CARRIERA E SIMULAZIONE DEL VOTO DI LAUREA</div>
                </div>
                       
                <table class="info-laureando">
                    <tr><td>Matricola:</td> <td> ' . $matricola . '</td></tr>
                    <tr><td>Nome:</td> <td>' . $laureando->getNome() . '</td></tr>
                    <tr><td>Cognome:</td> <td> ' . $laureando->getCognome() . '</td></tr>
                    <tr><td>Email:</td> <td> ' . $laureando->getEmail() . '</td></tr>
                    <tr><td>Data:</td> <td> ' . $laureando->getDataLaurea()->format("Y-m-d") . '</td></tr>
                    <tr><td>Bonus:</td> <td> ' . $bonus . '</td></tr>
                </table>';

        // inserimento esami nella tabella
        $html .= '
            <table class="esami">
                <tr>
                    <td class="myTh" style="width: 80%" >ESAME</td><td class="myTh">CFU</td><td class="myTh">VOT</td><td class="myTh">MED</td><td class="myTh">INF</td>
                </tr>';

        for($i = 0; $i < $laureando->getNumeroEsami(); $i++){
            $html .= '<tr>';

            $esame = $laureando->getEsame($i);
            $nomeEsame = $esame->getNomeEsame();

            $html .= '<td class="esame">' . $nomeEsame . '</td>';
            $html .= '<td>' . $esame->getPeso() . '</td>';
            $html .= '<td>' . $esame->getVoto() . '</td>';

            if($esame->getFaMedia()){
                $html .= '<td>X</td>';
            }else{
                $html .= '<td></td>';
            }

            if($laureando->isEsameInformatico($nomeEsame)){
                $html .= '<td>X</td>';
            }else{
                $html .= '<td></td>';
            }

            $html .= '</tr>';
        }

        $html .= '</table>';

        // inserimento dati calcolati nella terza sezione
        $mediaPesata = round($laureando->calcolaMediaPesata(), 3);
        $cfuTotaliMedia = round($laureando->calcolaCFUTotaliMedia(), 3);
        $cfuTotali = $laureando->calcolaCFUTotali();
        $votoTesi = 0;
        $mediaInformatica = round($laureando->calcolaMediaInformatica(), 3);

        $html .= '<table class="info-calcoli">
                    <tr><td style="width: 40%">Media Pesata (M):</td> <td> ' . $mediaPesata . '</td></tr>
                    <tr><td>Crediti che fanno media (CFU):</td> <td>' . $cfuTotaliMedia . '</td></tr>
                    <tr><td>Crediti curriculari conseguiti:</td> <td> ' . $cfuTotali .'/'. $cfuRichiesti . '</td></tr>
                    <tr><td>Voto di tesi (T):</td> <td>'. $votoTesi . '</td></tr>
                    <tr><td>Formula calcolo voto di laurea:</td> <td> ' . $formula . '</td></tr>
                    <tr><td>Media pesata esami INF:</td> <td> ' . $mediaInformatica . '</td></tr>
                </table>';

        return $html;
    }

    public function generaPDF($laureando) {

        // creazione istanza mpdf
        $mpdf = new \Mpdf\Mpdf([
            'format' => 'A4',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 10,
            'margin_bottom' => 10,
        ]);

        $cdl = $laureando->getCorsoDiLaurea();
        $gestioneCalcoloReportistica = new GestioneCalcoloReportistica();
        $parametri = json_decode($gestioneCalcoloReportistica->restituisciParametriCorsi(), true);
        $formula = $parametri['corsi'][$cdl]['formula_voto_string'];
        $cfuRichiesti = $parametri['corsi'][$cdl]['crediti_richiesti'];

        $html = $this->creaPaginaLaureando($laureando, $formula, $cfuRichiesti);

        // salvataggio file PDF
        $mpdf->WriteHTML($html);
        $mpdf->Output("../../src/views/prospettiPDF/{$laureando->getMatricola()}_pdf_laureando.pdf", 'F');
    }

    public function generaPDFConSimulazione($laureando){

        $pdf_simulazione = new \Mpdf\Mpdf([
            'format' => 'A4',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 10,
            'margin_bottom' => 10,
        ]);


        $cdl = $laureando->getCorsoDiLaurea();
        $gestioneCalcoloReportistica = new GestioneCalcoloReportistica();
        $parametri = json_decode($gestioneCalcoloReportistica->restituisciParametriCorsi(), true);
        $formula = $parametri['corsi'][$cdl]['formula_voto_string'];
        $cfuRichiesti = $parametri['corsi'][$cdl]['crediti_richiesti'];

        $html = $this->creaPaginaLaureando($laureando, $formula, $cfuRichiesti);

        // Aggiunta della simulazione voto alla pagina del prospetto del laureando
        $cMin = $parametri['corsi'][$cdl]['Cmin'];
        $cMax = $parametri['corsi'][$cdl]['Cmax'];
        $cStep = $parametri['corsi'][$cdl]['Cstep'];
        $tMin = $parametri['corsi'][$cdl]['Tmin'];
        $tMax = $parametri['corsi'][$cdl]['Tmax'];
        $tStep = $parametri['corsi'][$cdl]['Tstep'];
        $simulazione = new SimulazioneVotoLaurea($formula, $cMin, $cMax, $cStep, $tMin, $tMax, $tStep);
        $voti = $simulazione->effettuaSimulazioneVoto($laureando);
        $num_voti = count($voti);

        $html .= '<table class="simulazione" style="height: 50%">';
        $html .= '<tr><td class="myTh" colspan="2">SIMULAZIONE DI VOTO DI LAUREA</td></tr>';

        // varia solo il voto di commissione C
        $html .= '<tr>
                            <td class="myTh">VOTO COMMISSIONE (C)</td>
                            <td class="myTh">VOTO LAUREA</td>
                            </tr>';

        $p = $cMin;
        $step = $cStep;
        for($j = 0; $j < $num_voti; $j++){
            $html .= '<tr>
                                <td>' . $p . '</td>
                                <td>' . $voti[$j] . '</td>
                                </tr>';
            $p += $step;
        }

        $html .= '</table>';

        $html .= '<div class="info-voto-finale">VOTO DI LAUREA FINALE: ' . $parametri['corsi'][$cdl]['info_voto_finale'] . '</div>';

        $pdf_simulazione->WriteHTML($html);
        $pdf_simulazione->Output("../../src/views/prospettiPDF/{$laureando->getMatricola()}_pdf_simulazione.pdf", 'F');
    }
}
?>