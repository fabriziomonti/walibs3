wa_azioni_usabile

UI per waTabella in cui le azioni vengono implementate senza utilizzo di 
javascript, e quindi ogni azione ritorna semplicemente al server riportando
in GET quale azione l'utente ha scelto.

La UI, inoltre, non gestisce la possibilità di ordinamento e filtro.

Per i motivi sopra descritti è evidente che la scelta di questa UI condiziona
pesantemente la scrittura del codice lato server, e porta l'applicazione
a essere farraginosa fino ai limiti dell'insensato, e forse superandoli. Occorre 
quindi valutare se e quando è il caso di utilizzare una soluzione UI di questo 
tipo e non prenderla per ciò che vuole essere: un caso di scuola.