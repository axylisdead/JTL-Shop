{includeMailTemplate template=header type=plain}

Sehr {if $Kunde->cAnrede == "w"}geehrte{elseif $Kunde->cAnrede == "m"}geehrter{else}geehrte(r){/if} {$Kunde->cAnredeLocalized} {$Kunde->cNachname},

Ihre Bestellung vom {$Bestellung->dErstelldatum_de} mit Bestellnummer {$Bestellung->cBestellNr} wurde heute an Sie versandt.

{foreach name=pos from=$Bestellung->oLieferschein_arr item=oLieferschein}
    {if $oLieferschein->oVersand_arr|count > 1}
        Mit den nachfolgenden Links k�nnen Sie sich �ber den Status Ihrer Sendungen informieren:
    {else}
        Mit dem nachfolgendem Link k�nnen Sie sich �ber den Status Ihrer Sendung informieren:
    {/if}

    {foreach from=$oLieferschein->oVersand_arr item=oVersand}
        {if $oVersand->getIdentCode()|strlen > 0}
            Tracking-Url: {$oVersand->getLogistikVarUrl()}
            {if $oVersand->getHinweis()|strlen > 0}
                Tracking-Hinweis: {$oVersand->getHinweis()}
            {/if}
        {/if}
    {/foreach}
{/foreach}

Wir w�nschen Ihnen viel Spa� mit der Ware und bedanken uns f�r Ihren Einkauf und Ihr Vertrauen.

Mit freundlichem Gru�,
Ihr Team von {$Firma->cName}

{includeMailTemplate template=footer type=plain}