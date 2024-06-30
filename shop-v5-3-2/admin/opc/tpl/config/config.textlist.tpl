<div class="form-group" id="{$propname}-slides">
    <label>{$propdesc.label}</label>
    {foreach $propval as $i => $text}
        <div class="input-group">
            <div class="input-group-prepend">
                <button type="button" class="btn"
                        onclick="removeLine_{$propname}(this);">
                    <i class="fas fa-times fa-fw"></i>
                </button>
            </div>
            <label class="sr-only" for="{$propname}-{$i}"></label>
            <input type="text" class="form-control" name="{$propname}[]"
                   value="{$text|escape:'html'}" id="{$propname}-{$i}" data-index="{$i}">
            <div class="input-group-append">
                <span class="btn secondary btn-slide-mover">
                    <i class="fas fa-arrows-alt fa-fw"></i>
                </span>
            </div>
        </div>
    {/foreach}
</div>
<div class="input-group" id="new-input-group-{$propname}">
    <div class="input-group-prepend">
        <button type="button" class="btn primary"
                onclick="addNewLine_{$propname}()">
            <i class="fas fa-plus fa-fw"></i>
        </button>
    </div>
    <label class="sr-only" for="{$propname}-new"></label>
    <input type="text" class="form-control" id="{$propname}-new" disabled>
    <div class="input-group-append">
        <span type="button" class="btn secondary btn-slide-mover">
            <i class="fas fa-arrows-alt fa-fw invisible"></i>
        </span>
    </div>
</div>
<script>
    $('#{$propname}-slides').sortable({
        handle: '.btn-slide-mover'
    });

    function getNewDataIndex_{$propname}()
    {
        let maxIndex = 0;

        for(const input of $('#{$propname}-slides input[data-index]')) {
            if(parseInt(input.dataset.index) > maxIndex) {
                maxIndex = parseInt(input.dataset.index);
            }
        }

        return maxIndex + 1;
    }

    function removeLine_{$propname}(elm)
    {
        $(elm).closest('.input-group').remove();
    }

    function addNewLine_{$propname}()
    {
        let newInputGroup      = $('#new-input-group-{$propname}');
        let newInputGroupClone = newInputGroup.clone();

        newInputGroupClone.attr('id', '');
        newInputGroupClone.find('button')
            .removeClass('primary')
            .attr('onclick', 'removeLine_{$propname}(this);');
        newInputGroupClone.find('button i')
            .removeClass('fa-plus')
            .addClass('fa-times');
        newInputGroupClone.find('.btn-slide-mover i')
            .removeClass('invisible');
        newInputGroupClone.find('input')
            .prop('disabled', false)
            .attr('name', '{$propname}[]')
            .attr('data-index', getNewDataIndex_{$propname}());
        newInputGroupClone.appendTo('#{$propname}-slides');
    }
</script>