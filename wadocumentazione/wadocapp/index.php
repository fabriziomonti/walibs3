<?php
include "wadocapp.inc.php";

$appl = new waDocApp();

// prepara la pagina, ossia il contenitore della tabella
$appl->aggiungiElemento($appl->dammiMenu());
$appl->aggiungiElemento($appl->datiApplicazione["titolo"] . " - Gestione documentazione", "titolo");

// manda in output l'intera pagina
$appl->mostra();