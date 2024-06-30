{if isset($oMailObjekt->oLogEntry_arr)}
Log entries ({$oMailObjekt->oLogEntry_arr|@count}):

{foreach $oMailObjekt->oLogEntry_arr as $oLogEntry}
    [{$oLogEntry->dErstellt|date_format:"%d.%m.%Y %H:%M:%S"}] [{if $oLogEntry->nLevel == 1}Error{elseif $oLogEntry->nLevel == 2}Notice{elseif $oLogEntry->nLevel == 4}Debug{/if}]
{for $i=0 to $oLogEntry->cLog|strlen step 120}
        "{$oLogEntry->cLog|replace:"\n":' '|substr:$i:120}"
{/for}
{/foreach}
{/if}