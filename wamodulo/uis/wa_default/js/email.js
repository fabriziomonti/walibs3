//*****************************************************************************
//*****************************************************************************
//*****************************************************************************
/**
* classe waemail: controllo di input di un indirizzo email
* 
* @class waemail
* @extends wadata
*/
var waemail = new Class
(
	{
	//-------------------------------------------------------------------------
	// extends
	Extends: wacontrollo,

	//-------------------------------------------------------------------------
	// proprieta'
	tipo: 'email',
	
	//-------------------------------------------------------------------------
	verificaForma: function() 
		{
		if (this.obj.value == '')
			return true;
		//var emailPattern = /^(\w+(?:\.\w+)*)@((?:\w+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
		var emailPattern = /^[\w-\.]{1,}\@([\da-zA-Z-]{2,}\.){1,}[\da-zA-Z-]{2,4}$/i;
		return (emailPattern.test(this.obj.value) ? true : false);
			
		}
		
	}
);
