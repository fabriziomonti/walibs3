wa_azioni_sx_quick_edit

UI per waTabella in cui le azioni su riga vengono implementate sotto forma di
bottoni posizionati nella prima colonna a sinistra della tabella.

La tabella gestisce editing, ossia la possibilità di modificare/eliminare i dati 
presenti nella tabella, con aggiornamento immediato del dato (invio della 
modifica al server) alla perdita del focus del controllo di input (oppure di
richiesta di inserimento/eliminazione).

Si presti attenzione che l'inserimento immediato prevede, da parte del server,
di poter creare sulla base dati un record vuoto o comunque valorizzato con soli
va,lori di default.

Usa la struttura delle classi javascript definite in

../wa_file_comuni/js/watabella/js

e la cui documentazione è in 

../wa_file_comuni/js/watabella/js/doc

Per l'implementazione dei metodi relativi alle azioni si legga la nota alla
classe watabella della documentazione di cui sopra.