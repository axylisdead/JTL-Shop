{assign var=rounded value=''}
{assign var=useWebP value=$params.webp->getValue() === true && \JTL\Media\Image::hasWebPSupport()}
{if $params.rounded->getValue() !== false}
    {if $params.rounded->getValue() === true}
        {assign var=rounded value=' rounded'}
    {else}
        {assign var=rounded value=' rounded-'|cat:$params.rounded->getValue()}
    {/if}
{/if}

{if $params.src->hasValue() && strpos($params.src->getValue(), 'keinBild.gif') !== false}
<img class="{$params.class->getValue()} {$rounded} img-fluid{if $params['fluid-grow']->getValue() === true} w-100{/if}"
     height="{if $params.height->hasValue() && !empty($params.height->getValue())}{$params.height->getValue()}{else}130{/if}"
     width="{if $params.width->hasValue() && !empty($params.width->getValue())}{$params.width->getValue()}{else}130{/if}"
     {if $params.alt->hasValue()}alt="{$params.alt->getValue()}"{/if}
     src="{$params.src->getValue()}">
{else}
    {if $useWebP}
    <picture>
        <source
            {if $params.srcset->hasValue()}
                srcset="{$params.srcset->getValue()|regex_replace:"/\.(?i)(jpg|jpeg|png)/":".webp"}"
            {elseif $params.src->hasValue()}
                srcset="{$params.src->getValue()|regex_replace:"/\.(?i)(jpg|png)/":".webp"}"
            {else}
                srcset=""
            {/if}
            {if $params.sizes->hasValue()}sizes="{$params.sizes->getValue()}"{/if}
            {if $params.width->hasValue() && !empty($params.width->getValue())}width="{$params.width->getValue()}"{/if}
            {if $params.height->hasValue() && !empty($params.height->getValue())}height="{$params.height->getValue()}"{/if}
            type="image/webp">
    {/if}
        <img
            src="{$params.src->getValue()}"
            {if $params.srcset->hasValue()}srcset="{$params.srcset->getValue()}"{/if}
            {if $params.sizes->hasValue()}sizes="{$params.sizes->getValue()}"{/if}
            class="{strip}{$params.class->getValue()}{$rounded}
            {if $params.fluid->getValue() === true} img-fluid{/if}
            {if $params['fluid-grow']->getValue() === true} img-fluid w-100{/if}
            {if $params.thumbnail->getValue() === true} img-thumbnail{/if}
            {if $params.left->getValue() === true} float-left{/if}
            {if $params.right->getValue() === true} float-right{/if}
            {if $params.center->getValue() === true} mx-auto d-block{/if}{/strip}"
            {if $params.lazy->getValue() === true}loading="lazy"{/if}
            {if $params.id->hasValue()}id="{$params.id->getValue()}"{/if}
            {if $params.title->hasValue()}title="{$params.title->getValue()}"{/if}
            {if $params.alt->hasValue()}alt="{$params.alt->getValue()}"{/if}
            {if $params.width->hasValue() && !empty($params.width->getValue())}width="{$params.width->getValue()}"{/if}
            {if $params.height->hasValue() && !empty($params.height->getValue())}height="{$params.height->getValue()}"{/if}
            {if $params.style->hasValue()}style="{$params.style->getValue()}"{/if}
            {if $params.itemprop->hasValue()}itemprop="{$params.itemprop->getValue()}"{/if}
            {if $params.itemscope->getValue() === true}itemscope {/if}
            {if $params.itemtype->hasValue()}itemtype="{$params.itemtype->getValue()}"{/if}
            {if $params.itemid->hasValue()}itemid="{$params.itemid->getValue()}"{/if}
            {if $params.role->hasValue()}role="{$params.role->getValue()}"{/if}
            {if $params.aria->hasValue()}
                {foreach $params.aria->getValue() as $ariaKey => $ariaVal} aria-{$ariaKey}="{$ariaVal}" {/foreach}
            {/if}
            {if $params.data->hasValue()}
                {foreach $params.data->getValue() as $dataKey => $dataVal} data-{$dataKey}="{$dataVal}" {/foreach}
            {/if}
            {if $params.attribs->hasValue()}
                {foreach $params.attribs->getValue() as $key => $val} {$key}="{$val}" {/foreach}
            {/if}
        >
    {if $useWebP}
    </picture>
    {/if}
{/if}
