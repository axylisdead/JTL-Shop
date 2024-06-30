<script async defer data-namespace="{$apiNamespace}" src="{$apiURL}"></script>
<script>
    {literal}
    window.{/literal}{$apiNamespace}_render{literal} = {
        {/literal}{foreach name="renderPos" from=$positions key="pos" item="def"}{literal}
        {/literal}'{$pos}'{literal}: {
            selector: {/literal}'{$def.selector}'{literal},
            method: {/literal}'{$def.method}'{literal},
            minPrice: {/literal}{$settings->get($pos|cat:'_minprice', 0)}{literal},
            render: {
                placement: {/literal}'{if $pos === 'product'}product{else}cart{/if}'{literal},
                amount: {/literal}{if $pos === 'product'}{$productPrice}{else}{$wkPrice}{/if}{literal},
                style: {
                    layout: '{/literal}{$settings->get($pos|cat:'_layout', 'text')}{literal}',
                    logo: {
                        type: '{/literal}{$settings->get($pos|cat:'_logo_type', 'primary')}{literal}'
                    },
                    text: {
                        size: '{/literal}{$settings->get($pos|cat:'_text_size', '12')}{literal}',
                        color: '{/literal}{$settings->get($pos|cat:'_text_color', 'black')}{literal}'
                    }{/literal}{if $settings->get($pos|cat:'_layout', 'text') === 'flex'},
                    {literal}
                    color: '{/literal}{$settings->get($pos|cat:'_style_color', 'blue')}{literal}',
                    ratio: '{/literal}{$settings->get($pos|cat:'_style_ratio', '1x1')}{literal}'
                    {/literal}
                    {/if}{literal}
                }
            }
        }
        {/literal}{if !$smarty.foreach.renderPos.last},{/if}{/foreach}{literal}
    };
    (function() {
        let bannerTpls = {
            {/literal}{foreach name="tplPos" from=$positions key="pos" item="def"}{literal}
            {/literal}'{$pos}': '{include file="ppc_banner_{$pos}.tpl" id="{$apiNamespace}_container_{$pos}"}'{literal}
            {/literal}{if !$smarty.foreach.tplPos.last},{/if}{/foreach}{literal}
        };
        function ppcBannerLoad() {
            let data = window.{/literal}{$apiNamespace}_render{literal}
            for (let pos in data) {
                if (data.hasOwnProperty(pos)) {
                    ppcBannerPush(pos, data[pos]);
                }
            }
        }
        function ppcBannerPush(pos, data) {
            if (data.render.amount <= 0) {
                return;
            }
            let $container = $('#{/literal}{$apiNamespace}_container_{literal}' + pos);
            if ($container.length === 0) {
                $container = $(bannerTpls[pos]);
                $(data.selector)[data.method]($container);
            }
            if (data.minPrice <= data.render.amount) {
                data.render.onRender = function (e) {
                    $container.show();
                }
                if ((typeof {/literal}{$apiNamespace}{literal}) !== 'undefined') {
                    {/literal}{$apiNamespace}{literal}.Messages(data.render).render('.ppc-message.' + pos);
                }
            } else {
                $container.hide();
            }
        }
        $(window).on('load', function (e) {
            ppcBannerLoad();
        });
        $(document).on('evo:loaded.io.request', function (e, callData, x) {
            if (callData.req.name === 'getBasketItems' && callData.status === 'success') {
                let data = window.{/literal}{$apiNamespace}_render{literal}
                if ((typeof data.miniwk) !== 'undefined') {
                    data.miniwk.render.amount = parseFloat($('.cart-dropdown .total > strong').text().replace(',', '.'));
                    ppcBannerPush('miniwk', data.miniwk);
                }
            } else if (callData.req.name === 'jtl_paypal_get_presentment' && callData.status === 'success') {
                let data = window.{/literal}{$apiNamespace}_render{literal};
                if ((typeof data.product) !== 'undefined') {
                    data.product.render.amount = callData.req.params[0].price;
                    ppcBannerPush('product', data.product);
                }
            } else if (callData.req.name === 'buildConfiguration' && callData.status === 'success') {
                let data = window.{/literal}{$apiNamespace}_render{literal};
                if ((typeof data.product) !== 'undefined') {
                    data.product.render.amount = $.evo.article().response.fGesamtpreis[{/literal}{$netto}{literal}];
                    ppcBannerPush('product', data.product);
                }
            }
        });
        $(document).on('evo:loaded.evo.content', function (e, callData) {
            let data = window.{/literal}{$apiNamespace}_render{literal};

            if (callData.url.startsWith('bestellvorgang.php') && (typeof data.payment) !== 'undefined') {
                ppcBannerPush('payment', data.payment);
            } else if ((typeof data.product) !== 'undefined') {
                data.product.render.amount = $('meta[itemprop="price"]').attr('content');
                ppcBannerPush('product', data.product);
            }
        });
    })();
    {/literal}
</script>
