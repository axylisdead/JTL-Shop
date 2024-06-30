<div id="ppcBanner">
    <form method="post" action="plugin.php?kPlugin={$kPlugin}" enctype="multipart/form-data" name="wizard" class="settings navbar-form">
        {$jtl_token}
        <input type="hidden" name="kPlugin" value="{$kPlugin}" />
        <input type="hidden" name="kPluginAdminMenu" value="{$kPluginAdminMenu}" />
        <div class="panel">
            <div class="panel-heading">
                <h3 class="panel-title">PayPal Ratenzahlung Banner</h3>
            </div>
            <div class="panel-body">
                <div class="input-group ">
                    <span class="input-group-addon"><label for="settingPPCActive">PayPal Ratenzahlung Banner aktivieren:</label></span>
                    <span class="input-group-wrap">
                        <select class="form-control combo" name="ppcSetting[active]" id="settingPPCActive">
                            <option value="Y"{if $ppcSetting->get('active', 'N') === 'Y'} selected="selected"{/if}>Ja</option>
                            <option value="N"{if $ppcSetting->get('active', 'N') === 'N'} selected="selected"{/if}>Nein</option>
                        </select>
                    </span>
                    <span class="input-group-addon">
                        <button type="button" class="btn-tooltip btn btn-info btn-heading" data-html="true" data-toggle="tooltip" data-placement="left" title="" data-original-title="Aktivieren Sie hier die Nutzung des PayPal Ratenzahlung Banner.">
                            <i class="fa fa-question"></i>
                        </button>
                    </span>
                </div>
                <div class="input-group">
                    <span class="input-group-addon"><label for="settingClientID">PayPal-API-Client-ID:</label></span>
                    <span class="input-group-wrap">
                        <input type="text" class="form-control" name="ppcSetting[api_client_id]" id="settingClientID" value="{$ppcSetting->get('api_client_id', $apiClientID)}" placeholder="xxx" />
                    </span>
                    <span class="input-group-addon">
                        <button type="button" class="btn-tooltip btn btn-info btn-heading" data-html="true" data-toggle="tooltip" data-placement="left" title="" data-original-title="Tragen Sie hier die Client-ID ein.">
                            <i class="fa fa-question"></i>
                        </button>
                    </span>
                </div>
                {foreach $ppcPositions as $ppcPositionKey => $ppcPosition}
                    <div class="input-group">
                        <span class="input-group-addon"><label for="settingbanner_{$ppcPositionKey}_active">{$ppcPosition}:</label></span>
                        <span class="input-group-wrap">
                            <select class="form-control combo settingbanner_position_activate" name="ppcSetting[{$ppcPositionKey}_active]" id="settingbanner_{$ppcPositionKey}_active">
                                <option value="Y"{if $ppcSetting->get($ppcPositionKey|cat:'_active', 'N') === 'Y'} selected="selected"{/if}>Ja</option>
                                <option value="N"{if $ppcSetting->get($ppcPositionKey|cat:'_active', 'N') === 'N'} selected="selected"{/if}>Nein</option>
                            </select>
                        </span>
                        <span class="input-group-addon">
                            <button type="button" class="btn-tooltip btn btn-info btn-heading" data-html="true" data-toggle="tooltip" data-placement="left" title="" data-original-title="{$ppcPosition} aktivieren.">
                                <i class="fa fa-question"></i>
                            </button>
                        </span>
                    </div>
                {/foreach}
            </div>
        </div>
        <div>
            {foreach $ppcPositions as $ppcPositionKey => $ppcPosition}
                <div class="panel" id="settingbanner_{$ppcPositionKey}" >
                    <div class="panel-heading">
                        <h3 class="panel-title">{$ppcPosition}</h3>
                    </div>
                    <div class="panel-body">
                        <div class="input-group ">
                            <span class="input-group-addon"><label for="settingbanner_{$ppcPositionKey}_minprice">Mindestpreis:</label></span>
                            <span class="input-group-wrap">
                                <input type="number" step="1" class="form-control" name="ppcSetting[{$ppcPositionKey}_minprice]" id="settingbanner_{$ppcPositionKey}_minprice" value="{$ppcSetting->get($ppcPositionKey|cat:'_minprice', 100)|round|number_format:2}" />
                            </span>
                        </div>
                        <div class="input-group ">
                            <span class="input-group-addon"><label for="settingbanner_{$ppcPositionKey}_layout">Layout:</label></span>
                            <span class="input-group-wrap">
                                <select class="form-control combo settingbanner_layout_select" name="ppcSetting[{$ppcPositionKey}_layout]" id="settingbanner_{$ppcPositionKey}_layout">
                                    <option value="text"{if $ppcSetting->get($ppcPositionKey|cat:'_layout', 'text') === 'text'} selected="selected"{/if}>Contextual message</option>
                                    <option value="flex"{if $ppcSetting->get($ppcPositionKey|cat:'_layout', 'text') === 'flex'} selected="selected"{/if}>Responsive display banner</option>
                                </select>
                            </span>
                        </div>
                        <div class="input-group ">
                            <span class="input-group-addon"><label for="settingbanner_{$ppcPositionKey}_logo_type">Logo Type:</label></span>
                            <span class="input-group-wrap">
                                <select class="form-control combo" name="ppcSetting[{$ppcPositionKey}_logo_type]" id="settingbanner_{$ppcPositionKey}_logo_type">
                                    <option value="primary"{if $ppcSetting->get($ppcPositionKey|cat:'_logo_type', 'primary') === 'primary'} selected="selected"{/if}>Full Logo</option>
                                    <option value="alternative"{if $ppcSetting->get($ppcPositionKey|cat:'_logo_type', 'primary') === 'alternative'} selected="selected"{/if}>Monogram Logo</option>
                                    <option value="inline"{if $ppcSetting->get($ppcPositionKey|cat:'_logo_type', 'primary') === 'inline'} selected="selected"{/if}>Inline Logo</option>
                                    <option value="none"{if $ppcSetting->get($ppcPositionKey|cat:'_logo_type', 'primary') === 'none'} selected="selected"{/if}>Text only</option>
                                </select>
                            </span>
                        </div>
                        <div class="input-group ">
                            <span class="input-group-addon"><label for="settingbanner_{$ppcPositionKey}_text_size">Text Size:</label></span>
                            <span class="input-group-wrap">
                                <input type="number" step="1" class="form-control" name="ppcSetting[{$ppcPositionKey}_text_size]" id="settingbanner_{$ppcPositionKey}_text_size" value="{$ppcSetting->get($ppcPositionKey|cat:'_text_size', 12)|number_format}" />
                            </span>
                        </div>
                        <div class="input-group ">
                            <span class="input-group-addon"><label for="settingbanner_{$ppcPositionKey}_text_color">Text Color:</label></span>
                            <span class="input-group-wrap">
                                <select class="form-control combo" name="ppcSetting[{$ppcPositionKey}_text_color]" id="settingbanner_{$ppcPositionKey}_text_color">
                                    <option value="black"{if $ppcSetting->get($ppcPositionKey|cat:'_text_color', 'black') === 'black'} selected="selected"{/if}>Black text, colored logo</option>
                                    <option value="white"{if $ppcSetting->get($ppcPositionKey|cat:'_text_color', 'black') === 'white'} selected="selected"{/if}>White text, white logo</option>
                                </select>
                            </span>
                        </div>
                        <div class="input-group banner_{$ppcPositionKey}_flex_style">
                            <span class="input-group-addon"><label for="settingbanner_{$ppcPositionKey}_style_color">Flex Layout Type:</label></span>
                            <span class="input-group-wrap">
                                <select class="form-control combo" name="ppcSetting[{$ppcPositionKey}_style_color]" id="settingbanner_{$ppcPositionKey}_style_color">
                                    <option value="blue"{if $ppcSetting->get($ppcPositionKey|cat:'_style_color', 'white') === 'blue'} selected="selected"{/if}>Blue background, white text</option>
                                    <option value="black"{if $ppcSetting->get($ppcPositionKey|cat:'_style_color', 'white') === 'black'} selected="selected"{/if}>Black background, white text</option>
                                    <option value="white"{if $ppcSetting->get($ppcPositionKey|cat:'_style_color', 'white') === 'white'} selected="selected"{/if}>White background, blue text</option>
                                    <option value="gray"{if $ppcSetting->get($ppcPositionKey|cat:'_style_color', 'white') === 'gray'} selected="selected"{/if}>Light gray background, blue text</option>
                                </select>
                            </span>
                        </div>
                        <div class="input-group banner_{$ppcPositionKey}_flex_style">
                            <span class="input-group-addon"><label for="settingbanner_{$ppcPositionKey}_style_ratio">Flex Layout Ratio:</label></span>
                            <span class="input-group-wrap">
                                <select class="form-control combo" name="ppcSetting[{$ppcPositionKey}_style_ratio]" id="settingbanner_{$ppcPositionKey}_style_ratio">
                                    <option value="1x1"{if $ppcSetting->get($ppcPositionKey|cat:'_style_ratio', '1x1') === '1x1'} selected="selected"{/if}>Ratio of 1x1</option>
                                    <option value="1x4"{if $ppcSetting->get($ppcPositionKey|cat:'_style_ratio', '1x1') === '1x4'} selected="selected"{/if}>Ratio of 1x4</option>
                                    <option value="8x1"{if $ppcSetting->get($ppcPositionKey|cat:'_style_ratio', '1x1') === '8x1'} selected="selected"{/if}>Ratio of 8x1</option>
                                    <option value="20x1"{if $ppcSetting->get($ppcPositionKey|cat:'_style_ratio', '1x1') === '20x1'} selected="selected"{/if}>Ratio of 20x1</option>
                                </select>
                            </span>
                        </div>
                        <div class="input-group ">
                            <span class="input-group-addon"><label for="settingbanner_{$ppcPositionKey}_query_selector">PHPQuery Selektor:</label></span>
                            <span class="input-group-wrap">
                                <input type="text" class="form-control" name="ppcSetting[{$ppcPositionKey}_query_selector]" id="settingbanner_{$ppcPositionKey}_query_selector" value="{$ppcSetting->get($ppcPositionKey|cat:'_query_selector')}" />
                            </span>
                        </div>
                        <div class="input-group ">
                            <span class="input-group-addon"><label for="settingbanner_{$ppcPositionKey}_query_method">PHPQuery Methode:</label></span>
                            <span class="input-group-wrap">
                                <select class="form-control combo" name="ppcSetting[{$ppcPositionKey}_query_method]" id="settingbanner_{$ppcPositionKey}_query_method">
                                    <option value="after"{if $ppcSetting->get($ppcPositionKey|cat:'_query_method', 'after') === 'after'} selected="selected"{/if}>after</option>
                                    <option value="append"{if $ppcSetting->get($ppcPositionKey|cat:'_query_method', 'after') === 'append'} selected="selected"{/if}>append</option>
                                    <option value="before"{if $ppcSetting->get($ppcPositionKey|cat:'_query_method', 'after') === 'before'} selected="selected"{/if}>before</option>
                                    <option value="prepend"{if $ppcSetting->get($ppcPositionKey|cat:'_query_method', 'after') === 'prepend'} selected="selected"{/if}>prepend</option>
                                </select>
                            </span>
                        </div>
                        {if $apiConficured}
                            <div class="form-group form-row align-items-right">
                                <span class="input-group-wrap">
                                    <a href="#" class="btn btn-default btn-banner-preview" data-target="{$ppcPositionKey}">Vorschau</a>
                                </span>
                            </div>
                        {/if}
                    </div>
                </div>
            {/foreach}
        </div>
        <div class="save-wrapper">
            <button type="submit" class="btn btn-primary" name="task" value="savePPCSettings">
                <i class="fa fa-save"></i> Speichern
            </button>
        </div>
    </form>
</div>
{if $apiConficured && $apiURL}
    <div class="modal fade" id="ppcBannerPreviewModal" tabindex="-1" role="dialog" aria-labelledby="ppcBannerPreviewLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <strong id="ppcBannerPreviewLabel">PPC Banner Preview</strong>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="ppc-message"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Schlie&szlig;en</button>
                </div>
            </div>
        </div>
    </div>
    <script data-namespace="ppcSetting" src="{$apiURL}" ></script>
{/if}
<script>
    {literal}
    function ppcBannerPreview(banner) {
        let $form   = $('form.settings'),
            apiID   = {/literal}'{$ppcSetting->get('api_client_id', $apiClientID)}'{literal};

        if (apiID !== $('[name="ppcSetting[api_client_id]"]', $form).val()) {
            window.alert('Bitte speichern Sie Ihre &Auml;nderungen, um die Vorschau nutzen zu k&ouml;nnen..');

            return;
        }
        let $modal = $('#ppcBannerPreviewModal'),
            layout = $('[name="ppcSetting[' + banner + '_layout]"]', $form).val(),
            data   = {
                amount: Math.floor(Math.random() * 500) + 100,
                style: {
                    layout: layout,
                    logo: {
                        type: $('[name="ppcSetting[' + banner + '_logo_type]"]', $form).val()
                    },
                    text: {
                        size: $('[name="ppcSetting[' + banner + '_text_size]"]', $form).val(),
                        color: $('[name="ppcSetting[' + banner + '_text_color]"]', $form).val()
                    }
                },
                onRender: function () {
                    $modal.modal('show');
                }
            };

        if (layout === 'flex') {
            data.style.color = $('[name="ppcSetting[' + banner + '_style_color]"]', $form).val();
            data.style.ratio = $('[name="ppcSetting[' + banner + '_style_ratio]"]', $form).val();
        }

        let ppc = ppcSetting.Messages(data);
        ppc.render('#ppcBannerPreviewModal .ppc-message');
    }
    $('.btn-banner-preview').on('click', function (e) {
        e.preventDefault();
        ppcBannerPreview($(this).data('target'))
    });
    {/literal}
</script>