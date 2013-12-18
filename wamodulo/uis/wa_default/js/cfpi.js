//*****************************************************************************
//*****************************************************************************
//*****************************************************************************
/**
* classe wacfpi: controllo di input di un codice fiscale e/o partita IVA
* 
* @class wacfpi
* @extends wacontrollo
*/
var wacfpi = new Class
(
	{
	//-------------------------------------------------------------------------
	// extends
	Extends: wacontrollo,

	//-------------------------------------------------------------------------
	// proprieta'
	tipo: 'cfpi',
	gestioneCF: true,
	gestionePI: false,
		
	//-------------------------------------------------------------------------
	//initialization
	initialize: function(modulo, nome, valore, visibile, solaLettura, obbligatorio) 
		{
		// definizione iniziale delle proprieta'
		this.parent(modulo, nome, valore, visibile, solaLettura, obbligatorio);
		this.gestioneCF = this.obj.gestioneCF == '1';
		this.gestionePI = this.obj.gestionePI == '1';
		
		},
		
	//-------------------------------------------------------------------------
	verificaForma: function() 
		{
		if (this.obj.value == '')
			return true;
		
		var tester = this.obj.value.substring(0, 1).toUpperCase();
		if ((/^[A-Z]$/).test(tester) && this.gestioneCF)
			return this.verificaCF();
		else if ((/^\d$/).test(tester) && this.gestionePI)
			return this.verificaPI();

		return false;
		},
		
	//-------------------------------------------------------------------------
	verificaCF: function() 
		{
		var cf = this.obj.value.toUpperCase();
		var set1 = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		var set2 = "ABCDEFGHIJABCDEFGHIJKLMNOPQRSTUVWXYZ";
		var setpari = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		var setdisp = "BAKPLCQDREVOSFTGUHMINJWZYX";
		var s = 0;
		for( i = 1; i <= 13; i += 2 )
		   s += setpari.indexOf( set2.charAt( set1.indexOf( cf.charAt(i) )));
		for( i = 0; i <= 14; i += 2 )
		   s += setdisp.indexOf( set2.charAt( set1.indexOf( cf.charAt(i) )));
		if ( s%26 != cf.charCodeAt(15)-'A'.charCodeAt(0) )
			{
			// il check digit è sbagliato...
//			alert(String.fromCharCode(s % 26 + 65));
			return false;
			}
		
		var cfReg = /^[A-Z]{6}\d{2}[A-EHLMPRST]\d{2}[A-Z]\d{3}[A-Z]$/;
		if (cfReg.test(cf))
			// codice standard, non c'e' bisogno di controllare omocodia
			return true;

		// verifichiamo che la parte letterale sia composta effettivamente di
		// lettere e che la parte normalmente numerica contenga solo lettere
		// previste nei casi di omocodia
		cfReg = /^[A-Z]{6}[\dL-NP-V]{2}[A-EHLMPRST][\dL-NP-V]{2}[A-Z][\dL-NP-V]{3}[A-Z]$/;
		if (!cfReg.test(cf))
			return false;

		// una eventuale lettera al posto del numero indicante la decina del mese
		// non può essere una U o una V (corrispondenti a 8 e 9)
		cfReg = /^[\dA-Z]{9}[\dL-NP-T][\dA-Z]{6}$/;
		if (!cfReg.test(cf))
			return false;

		return true;
		},
		
	//-------------------------------------------------------------------------
	verificaPI: function() 
		{
		var piva = this.obj.value;

		var piReg = /^\d{11}$/;
		if (!piReg.test(piva))
			// composizione sbagliata
			return false;

        var X = 0 ;
        var Y = 0 ;
        var Z = 0 ;
    
        // cifre posto dispari ... ma per un array indicizzato a zero, la prima cifra ha indice zero ... appunto !
        X += parseInt( piva.charAt(0) ) ;
        X += parseInt( piva.charAt(2) ) ;
        X += parseInt( piva.charAt(4) ) ;
        X += parseInt( piva.charAt(6) ) ;
        X += parseInt( piva.charAt(8) ) ;

        // cifre posto pari ... ma per un array indicizzato a zero, la prima cifra ha indice uno ...
        Y += 2 * parseInt( piva.charAt(1) ) ;    if ( parseInt( piva.charAt(1) ) >= 5 ) Z++ ;
        Y += 2 * parseInt( piva.charAt(3) ) ;    if ( parseInt( piva.charAt(3) ) >= 5 ) Z++ ;
        Y += 2 * parseInt( piva.charAt(5) ) ;    if ( parseInt( piva.charAt(5) ) >= 5 ) Z++ ;
        Y += 2 * parseInt( piva.charAt(7) ) ;    if ( parseInt( piva.charAt(7) ) >= 5 ) Z++ ;
        Y += 2 * parseInt( piva.charAt(9) ) ;    if ( parseInt( piva.charAt(9) ) >= 5 ) Z++ ;
        
        var T = ( X + Y + Z ) % 10 ;
        var C = ( 10 - T ) % 10 ;

        return ( piva.charAt( piva.length - 1 ) == C );
		}
		
	}
);
