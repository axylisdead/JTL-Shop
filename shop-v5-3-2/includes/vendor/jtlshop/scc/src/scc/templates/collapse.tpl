{if $params['clip-text']->hasValue()}
    <{$params.tag->getValue()} id="clip-text-{$params.id->getValue()}">
    {$params['clip-text']->getValue()}
    </{$params.tag->getValue()}>
{/if}
<{$params.tag->getValue()}
class="collapse {if $params['is-nav']->getValue() === true} navbar-collapse{/if}
{if $params['button-label-show']->hasValue() && $params['button-label-hide']->hasValue()} collapse-with-button{/if}
{if $params['clip-text']->hasValue()} collapse-with-clip{/if}
{$params.class->getValue()}{if $params.visible->getValue() === true} show{/if}"
{if $params.id->hasValue()}id="{$params.id->getValue()}"{/if}
{if $params.style->hasValue()}style="{$params.style->getValue()}"{/if}
{if $params.itemprop->hasValue()}itemprop="{$params.itemprop->getValue()}"{/if}
{if $params.itemscope->getValue() === true}itemscope {/if}
{if $params.itemtype->hasValue()}itemtype="{$params.itemtype->getValue()}"{/if}
{if $params.itemid->hasValue()}itemid="{$params.itemid->getValue()}"{/if}
{if $params.role->hasValue()}role="{$params.role->getValue()}"{/if}
{if $params.title->hasValue()} title="{$params.title->getValue()}"{/if}
{if $params.aria->hasValue()}{foreach $params.aria->getValue() as $ariaKey => $ariaVal}aria-{$ariaKey}="{$ariaVal}" {/foreach} {/if}
{if $params.data->hasValue()}{foreach $params.data->getValue() as $dataKey => $dataVal}data-{$dataKey}="{$dataVal}" {/foreach} {/if}
{if $params.attribs->hasValue()}
    {foreach $params.attribs->getValue() as $key => $val} {$key}="{$val}" {/foreach}
{/if}
>
{$blockContent}
</{$params.tag->getValue()}>
{if $params['button-label-show']->hasValue() && $params['button-label-hide']->hasValue()}
    <a class="{$params['button-label-class']->getValue()}" data-toggle="collapse" href="#{$params.id->getValue()}"
       role="button" aria-expanded="{if $params.visible->getValue() === true}true{else}false{/if}" aria-controls="{$params.id->getValue()}"
       data-label-show="{$params['button-label-show']->getValue()}"
       data-label-hide="{$params['button-label-hide']->getValue()}">
        {if $params.visible->getValue() === true}
            {$params['button-label-hide']->getValue()}
        {else}
            {$params['button-label-show']->getValue()}
        {/if}
    </a>
{/if}
