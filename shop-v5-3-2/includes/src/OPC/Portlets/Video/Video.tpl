{if $isPreview}
    <div {$instance->getAttributeString()} class="opc-Video" style="position: relative">
        {if !empty($instance->getProperty('video-responsive'))}
            {$style = 'width:100%;'}
        {else}
            {$style = 'width:'}
            {$style = $style|cat:$instance->getProperty('video-width')}
            {$style = $style|cat:'px;height:'}
            {$style = $style|cat:$instance->getProperty('video-height')}
            {$style = $style|cat:'px;'}
        {/if}

        {$src = $portlet->getPreviewImageUrl($instance)}

        {if $src !== null && $instance->getProperty('video-vendor') === 'youtube'}
            {image src=$src alt='YouTube Video' fluid=true style=$style}
            <div class="give-consent-preview" style="{$style}background-image: url({$portlet->getPreviewOverlayUrl()})"></div>
        {elseif $src !== null && $instance->getProperty('video-vendor') === 'vimeo'}
            {image src=$src alt='Vimeo Video' fluid=true style=$style}
            <div class="give-consent-preview" style="{$style}background-image: url({$portlet->getPreviewOverlayUrl()})"></div>
        {else}
            <div>
                <i class="fas fa-film"></i>
                <span>{__('Video')}</span>
            </div>
        {/if}
    </div>
{else}
    <div id="{$instance->getUid()}" {$instance->getAttributeString()} class="opc-Video {$instance->getStyleClasses()}">
        {include 'snippets/video.tpl'
            video=$instance->video
            title=$instance->getProperty('video-title')|default:''|escape:'html'
            class='opc-Video-iframe-wrapper'
            responsive=$instance->getProperty('video-responsive')
        }
    </div>
{/if}