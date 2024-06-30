{$style = $instance->getProperty('listStyle')}
{$uid = $instance->getUid()}

{$displayCountSM = $instance->getProperty('displayCountSM')}
{$displayCountMD = $instance->getProperty('displayCountMD')}
{$displayCountLG = $instance->getProperty('displayCountLG')}
{$displayCountXL = $instance->getProperty('displayCountXL')}

{if $isPreview}
    <div class="opc-ProductStream" style="{$instance->getStyleString()}">
        {image alt='ProductStream' src=$portlet->getBaseUrl()|cat:'preview.'|cat:$style|cat:'.png'}
    </div>
{else}
    {$productlist = $portlet->getFilteredProducts($instance)}

    {if $style === 'list' || $style === 'gallery'}
        {if $style === 'list'}
            {$grid = '12'}
            {$eqHeightClasses = ''}
        {else}
            {$grid   = '6'}
            {$gridmd = '4'}
            {$gridxl = '3'}
            {$eqHeightClasses = 'row-eq-height row-eq-img-height'}
        {/if}
        {if $inContainer === false}
            <div class="container-fluid">
        {/if}
        {row class="$style $eqHeightClasses product-list opc-ProductStream opc-ProductStream-$style
                    {$instance->getStyleClasses()}"
            itemprop="mainEntity"
            itemscope=true
            itemtype="https://schema.org/ItemList"
            style="{$instance->getStyleString()}"}
            {foreach $productlist as $Artikel}
                {col cols={$grid} md="{if isset($gridmd)}{$gridmd}{/if}" xl="{if isset($gridxl)}{$gridxl}{/if}"
                     class="product-wrapper {if !($style === 'list' && $Artikel@last)}mb-4{/if}"
                     itemprop="itemListElement" itemscope=true itemtype="https://schema.org/Product"}
                    {if $style === 'list'}
                        {include file='productlist/item_list.tpl' tplscope=$style isOPC=true
                            idPrefix=$instance->getUid()}
                    {elseif $style === 'gallery'}
                        {include file='productlist/item_box.tpl' tplscope=$style class='thumbnail'
                            idPrefix=$instance->getUid()}
                    {/if}
                {/col}
            {/foreach}
        {/row}
        {if $inContainer === false}
            </div>
        {/if}
    {elseif $style === 'slider' || $style === 'simpleSlider'}
        {if $inContainer === false}
            <div class="container-fluid">
        {/if}
        <div class="opc-product-slider {$instance->getStyleClasses()} opc-ProductStream-{$style}"
             style="{$instance->getStyleString()}">
            {include file='snippets/product_slider.tpl' productlist=$productlist isOPC=true
                noCaptionSlider = ($style === 'simpleSlider')
                displayCounts = [$displayCountSM,$displayCountMD,$displayCountLG,$displayCountXL]
            }
        </div>
        {if $inContainer === false}
            </div>
        {/if}
    {elseif $style === 'box-slider'}
        <div class="opc-product-slider {$instance->getStyleClasses()} opc-ProductStream-{$style}"
             style="{$instance->getStyleString()}">
            {include file='snippets/product_slider.tpl' productlist=$productlist isOPC=true
                tplscope='box'
            }
        </div>
    {/if}
{/if}
