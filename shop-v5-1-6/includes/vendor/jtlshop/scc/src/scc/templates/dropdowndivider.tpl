<{$params.tag->getValue()} role="separator" class="dropdown-divider {$params.class->getValue()}"
    {if $params.id->hasValue()}id="{$params.id->getValue()}"{/if}
    {if $params.title->hasValue()} title="{$params.title->getValue()}"{/if}
    {if $params.style->hasValue()}style="{$params.style->getValue()}"{/if}
    {if $params.attribs->hasValue()}
        {foreach $params.attribs->getValue() as $key => $val} {$key}="{$val}" {/foreach}
    {/if}
>
</{$params.tag->getValue()}>
