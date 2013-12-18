<?php
include "applicazionetest.inc.php";

$appl = new applicazionetest();
if ($_GET['navigazione'])
	$appl->preferenzeUtente['navigazione'] = $_GET['navigazione'];

$appl->ridireziona("tabellacorsi.php");

