<?php
session_start();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generatore Prospetti di Laurea</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            align-content: center;
        }
        #container {
            display: block;
            width: fit-content;
            height: fit-content;
            text-align: left;
            background-color: lightblue;
            color: darkblue;
            border: 2px solid darkblue;
        }
        h2 {
            text-align: center;
        }
        form {
            display: flex;
            padding: 3em;
            font-weight: bold;
        }
        textarea, select, input {
            margin: 2em;
        }

        button {
            padding: 2em;
        }
        div.bottone {
            margin: 3em;
        }
        textarea {
            height: 14em;
        }
        select {
            height: 2em;
            width: 12em;
        }
        input {
            height: 2em;
        }
        #bottoni {
            display: block;
            align-content: center;
            text-align: center;
        }
        #generaProspettiPDF, #inviaProspettiPDF {
            padding: 0.5em;
            border: 2px solid darkblue;
            text-align: center;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            color: darkblue;
        }
        #generaProspettiPDF:hover, #inviaProspettiPDF:hover {
            background-color: lightskyblue;
        }

        #accessoProspettiPDF {
            background: none;
            border: none;
            padding: 0;
            font-weight: bold;
            color: darkblue;
            text-decoration: underline;
            cursor: pointer;
        }

        .info-msg {
            color: red;
        }
    </style>
</head>
<body>

<div id="container">
    <h2>Generatore prospetti di laurea</h2>
    <form method="post" action="./src/controllers/GeneratoreProspetti.php">
        <div id="cdl-data">
            <label>Cdl:</label><br>
            <select id="cdl" name="cdl">
                <option value="" disabled selected>Seleziona un Cdl</option>
                <option value="T. Ing. Informatica">T. Ing. Informatica</option>
                <option value="T. Ing. delle Telecomunicazioni">T. Ing. delle Telecomunicazioni</option>
                <option value="T. Ing. Biomedica">T. Ing. Biomedica</option>
                <option value="T. Ing. Aereospaziale">T. Ing. Aereospaziale</option>
                <option value="T. Ing. Elettronica">T. Ing. Elettronica</option>
                <option value="M. Ing. Biomedica, Bionics Engineering">M. Ing. Biomedica, Bionics Engineering</option>
                <option value="M. Ing. Elettronica">M. Ing. Elettronica</option>
                <option value="M. Computer Engineering, Artificial Intelligence and Data Engineering">M. Computer Engineering, Artificial Intelligence and Data Engineering</option>
                <option value="M. Ing. Robotica e della Automazione">M. Ing. Robotica e della Automazione</option>
                <option value="M. Ing. delle Telecomunicazioni">M. Ing. delle Telecomunicazioni</option>
                <option value="M. Cybersecurity">M. Cybersecurity</option>
            </select><br>

            <label>Data Laurea:</label><br>
            <input type="date" id="data" name="dataLaurea">
        </div>

        <div id="area-matricole">
            <label>Matricole:</label><br>
            <textarea id="matricole" name="matricole"></textarea>
        </div>

        <div id="bottoni">
            <div class="bottone">
                <!-- setto in php variabile sessione "generazione" -->
                <button id="generaProspettiPDF" name="generazione" type="submit">Crea prospetti</button>
            </div>
            <div class="bottone">
                <!-- setto in php variabile sessione "accesso" -->
                <button id="accessoProspettiPDF" name="accesso" type="submit">apri prospetti</button>
            </div>
            <div class="bottone">
                <!-- setto in php variabile sessione "invio" -->
                <button id="inviaProspettiPDF" name="invio" type="submit">Invia prospetti</button>
            </div>
            <?php
                //Messaggi di feedback per casi di generazione o invio prospetti

                if(isset($_SESSION['generati'])){
                    echo '<div class="info-msg">Prospetti generati</div>';
                    unset($_SESSION['generati']);
                }

                if(isset($_SESSION['inviati']) && isset($_SESSION['num_matricole'])){
                    echo '<div class="info-msg">Inviati '. $_SESSION['inviati'].'/'. $_SESSION['num_matricole'] . ' prospetti</div>';
                    unset($_SESSION['inviati']);
                    unset($_SESSION['num_matricole']);
                }
            ?>
        </div>

    </form>
</div>

</body>
</html>
