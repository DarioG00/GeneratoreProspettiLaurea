<?php
require_once '../../lib/Mpdf/vendor/autoload.php';
require_once '../../src/models/CarrieraLaureando.php';
require_once '../../src/models/CarrieraLaureandoInformatica.php';
require_once '../../src/models/Esame.php';
require_once '../../src/models/SimulazioneVotoLaurea.php';

class ProspettoPDFCommissione {

    public function __construct()
    {
    }

    public function generaPDF($laureandi) {

        // istanza mpdf
        $mpdf = new \Mpdf\Mpdf([
            'format' => 'A4',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 10,
            'margin_bottom' => 10,
        ]);

        // creazione pagina inziale con la lista dei laureandi (inizialmente vuota)
        $cdl = $laureandi[0]->getCorsoDiLaurea();

        $html = '
                <style>
                    body { font-family: Arial, sans-serif; margin: 0em; padding: 0em;}
                    
                    .intestazione div { text-align: center; font-size: 14px; margin: 1em; padding: 0em; }
                    
                    .lista-laureandi { width: 100%; border: 1px solid black; border-collapse: collapse; margin-bottom: 0.5em; margin-top: 0em; padding: 0em; }
                    .lista-laureandi td { text-align: center; font-size: 12px; padding: 2px; border: 1px solid black; }
                    .lista-laureandi .myTh { font-size: 14px; width: 25%;}
                    .laureando td { text-align: center; font-size: 12px; border: 1px solid black; margin: 0em; padding: 0em;}
                </style>
                
                <div class="intestazione">
                    <div>' . $cdl . '</div>
                    <div style="font-size: 12px">LAUREANDOSI 2 - Progettazione: mario.cimino@unipi.it, Amministrazione: rose.rossiello@unipi.it</div>
                    <div>LISTA LAUREANDI</div>
                </div>';

        // Aggiunta alla lista della pagina iniziale il nome e cognome dei laureandi
        $html .= '<table class="lista-laureandi">
                    <tr>
                        <td class="myTh">COGNOME</td>
                        <td class="myTh">NOME</td>
                        <td class="myTh">CDL</td>
                        <td class="myTh">VOTO LAUREA</td>
                    </tr>';

        // lista dei laureandi
        for($i = 0; $i < count($laureandi); $i++){
            $html .= '<tr>';

            $html .= '<td class="laureando">'. $laureandi[$i]->getCognome() . '</td>';
            $html .= '<td class="laureando">'. $laureandi[$i]->getNome() . '</td>';
            $html .= '<td class="laureando"></td>';
            $html .= '<td class="laureando"> /110 </td>';

            $html .= '</tr>';
        }
                
        $html .= '</table>';

        $mpdf->WriteHTML($html);

        // Aggiunta delle pagine con il pdf dei laureandi
        for($i = 0; $i < count($laureandi); $i++){

            $mpdf->setSourceFile("../../src/views/prospettiPDF/{$laureandi[$i]->getMatricola()}_pdf_simulazione.pdf");

            $tplId = $mpdf->importPage(1);
            $mpdf->AddPage();
            $mpdf->useTemplate($tplId);
        }

        $mpdf->Output("../../src/views/prospettiPDF/prospetto_commissione.pdf", 'F');
	}
}
?>