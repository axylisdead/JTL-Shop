{includeMailTemplate template=header type=plain}

Sehr {if $Kunde->cAnrede == "w"}geehrte{elseif $Kunde->cAnrede == "m"}geehrter{else}geehrte(r){/if} {$Kunde->cAnredeLocalized} {$Kunde->cNachname},

Sie erhalten im Rahmen der Aktion Kunden werben Kunden ein Guthaben von {$BestandskundenBoni->fGuthaben}. 

Wir bedanken uns f�r Ihre Teilnahme!

Mit freundlichem Gru�,
Ihr Team von {$Firma->cName}

{includeMailTemplate template=footer type=plain}