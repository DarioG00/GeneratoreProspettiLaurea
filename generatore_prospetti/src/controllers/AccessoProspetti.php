<?php
class AccessoProspetti
{
    public function __construct()
    {
    }

    public function accediProspetti(){

        header('Location: ../../src/views/prospettiPDF/prospetto_commissione.pdf');
    }
}