<?php
error_reporting(E_ERROR|E_WARNING);
include "../wamenu.inc.php";

// main flow ******

// intestazione della pagina html...
$outputBuffer = creaTop();

// output del menu
$outputBuffer .= dammiMenu()->mostra(true);

// corpo della pagina
$outputBuffer .= dammiCorpoPagina();

// chiusura della pagina html
$outputBuffer .= creaFooter();

// risistema il codice html rendendolo "strict"
$outputBuffer = fammeloStrict($outputBuffer);

// output del buffer contenente la pagina 
exit($outputBuffer);

//*****************************************************************************
function creaTop()
	{
	// intestazione della pagina html...
	header("Content-Type: text/html; charset=UTF-8");
	$buffer = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'>
			<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='it' lang='it'>";

	$buffer .= "<head><title>test waMenu</title><meta http-equiv='Content-Type' content='text/html;charset=utf-8' /></head>\n";
	$buffer .= "<body>\n";
	
	return $buffer;
	}
	
//*****************************************************************************
function creaFooter()
	{
	
	// chiusura della pagina html
	$buffer .= "</body></html>";

	return $buffer;
	}
	
//*****************************************************************************
function fammeloStrict($buffer)
	{
	// spostiamo eventuali "<link ...>" nell'head per essere "strict"
	list ($pre_head, $resto) = explode("<head>", $buffer, 2);
	list ($head, $resto) = explode("</head>", $resto, 2);
	list ($tra_head_e_body, $body) = explode("<body", $resto, 2);
	while (true)
		{
		list ($prima, $dopo) = explode("<link ", $body, 2);
		if (!$dopo)
			break;
		list ($link, $dopo) = explode(">", $dopo, 2);
		$head .= "<link $link" . (substr(rtrim($link), -1) == "/" ? '' : " /") . ">\n";
		$body = "$prima$dopo";
		}

	$buffer = "$pre_head\n<head>\n$head</head>$tra_head_e_body\n<body$body";

	return $buffer;
	}

//*****************************************************************************
/**
 * 
 * @return waMenu
 */
function dammiMenu()
	{

	$xsltType = $_GET['type'] ?  $_GET['type'] : "wa_orizzontale_default";
	$xslt = "../uis/$xsltType/xslt/wamenu.xsl";

	// crea il menu
	$m = new waMenu($xslt);
	$m->apri();

	$m->apriSezione("WebAppls");
		$m->aggiungiVoce("Sito Istituzionale",  "http://www.webappls.com");
		$m->aggiungiVoce("Area Clienti", "http://www.webappls.com/extranet.php");
		$m->aggiungiVoce("Intranet", "http://www.webappls.com/intranet.php");
		$m->apriSezione("Sottomenu");
			$m->aggiungiVoce("chi siamo",  "http://www.webappls.com/chi_siamo.php");
		$m->apriSezione("Sottomenu");
			$m->aggiungiVoce("chi siamo",  "http://www.webappls.com/chi_siamo.php");
			$m->aggiungiVoce("certificazioni",  "http://www.webappls.com/certificazioni.php");
			$m->aggiungiVoce("tecnologie",  "http://www.webappls.com/tecnologie.php");
			$m->aggiungiVoce("progetti",  "http://www.webappls.com/progetti.php");
		$m->apriSezione("Sottomenu");
			$m->aggiungiVoce("chi siamo",  "http://www.webappls.com/chi_siamo.php");
			$m->aggiungiVoce("certificazioni",  "http://www.webappls.com/certificazioni.php");
			$m->aggiungiVoce("tecnologie",  "http://www.webappls.com/tecnologie.php");
			$m->aggiungiVoce("progetti",  "http://www.webappls.com/progetti.php");
		$m->apriSezione("Sottomenu");
			$m->aggiungiVoce("chi siamo",  "http://www.webappls.com/chi_siamo.php");
			$m->aggiungiVoce("certificazioni",  "http://www.webappls.com/certificazioni.php");
			$m->aggiungiVoce("tecnologie",  "http://www.webappls.com/tecnologie.php");
			$m->aggiungiVoce("progetti",  "http://www.webappls.com/progetti.php");
		$m->apriSezione("Sottomenu");
			$m->aggiungiVoce("chi siamo",  "http://www.webappls.com/chi_siamo.php");
			$m->aggiungiVoce("certificazioni",  "http://www.webappls.com/certificazioni.php");
			$m->aggiungiVoce("tecnologie",  "http://www.webappls.com/tecnologie.php");
			$m->aggiungiVoce("progetti",  "http://www.webappls.com/progetti.php");
			$m->aggiungiVoce("clienti",  "http://www.webappls.com/clienti.php");
			$m->aggiungiVoce("partner",  "http://www.webappls.com/partner.php");
		$m->chiudiSezione();
			$m->aggiungiVoce("clienti",  "http://www.webappls.com/clienti.php");
			$m->aggiungiVoce("partner",  "http://www.webappls.com/partner.php");
		$m->chiudiSezione();
			$m->aggiungiVoce("clienti",  "http://www.webappls.com/clienti.php");
			$m->aggiungiVoce("partner",  "http://www.webappls.com/partner.php");
		$m->chiudiSezione();
			$m->aggiungiVoce("clienti",  "http://www.webappls.com/clienti.php");
			$m->aggiungiVoce("partner",  "http://www.webappls.com/partner.php");
		$m->chiudiSezione();
			$m->aggiungiVoce("certificazioni",  "http://www.webappls.com/certificazioni.php");
			$m->aggiungiVoce("tecnologie",  "http://www.webappls.com/tecnologie.php");
			$m->aggiungiVoce("progetti",  "http://www.webappls.com/progetti.php");
			$m->aggiungiVoce("clienti",  "http://www.webappls.com/clienti.php");
			$m->aggiungiVoce("partner",  "http://www.webappls.com/partner.php");
		$m->chiudiSezione();
	$m->chiudiSezione();

	$m->apriSezione("Look");
		$m->aggiungiVoce("A tendina",  "?wa_orizzontale_default");
		$m->aggiungiVoce("A scomparsa",  "?type=wa_orizzontale_scomparsa");
		$m->aggiungiVoce("A scomparsa automatica",  "?type=wa_orizzontale_scomparsa_automatica");
		$m->aggiungiVoce("Sempre al top",  "?type=wa_orizzontale_sempre_visibile");
		$m->aggiungiVoce("Verticale",  "?type=wa_verticale");
		$m->aggiungiVoce("Ad albero",  "?type=wa_albero");
		$m->aggiungiVoce("Usabile",  "?type=wa_usabile");
	$m->chiudiSezione();
	$m->apriSezione("walibs test");
		$m->aggiungiVoce("waDB",  "../../wadb/test/");
		$m->aggiungiVoce("waMenu",  "../../wamenu/test/");
		$m->aggiungiVoce("waModulo",  "../../wamodulo/test/");
		$m->aggiungiVoce("waTabella",  "../../watabella/test/");
		$m->aggiungiVoce("waApplicazione",  "../../waapplicazione/test/");
	$m->chiudiSezione();
	$m->apriSezione("Logout", "http://www.playboy.com/");
	$m->chiudiSezione();
	$m->chiudi();
	if ($_GET['xml'])
		{
		$m->mostraXML();
		exit();
		}
		
	return $m;
	}

//*****************************************************************************
function dammiCorpoPagina()
	{

	return "

	<p style='margin-top: 50px'>
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	il mattino ha l'oro in bocca il mattino ha l'oro in bocca il mattino ha l'oro in bocca <br />
	</p>
	<p>
	(Jack <b>\"Wendy, sono a casa tesoro\"</b> Nicholson, Shining...)
	</p>
	";
	
	}