<div class="form-group">
    <label for="config-{$propname}">{$propdesc.label}</label>
    <div id="config-{$propname}-picker">
        <div id="iconpicker-{$propname}" class="iconpickerly" data-placement="inline" style="display: none"></div>
    </div>
    <input type="hidden" id="config-{$propname}" name="{$propname}" value="{$propval|escape:'html'}">
    <script>
        $(() => {
            let iconpicker = $('#iconpicker-{$propname}');

            iconpicker.iconpicker();
            iconpicker.find('.popover-title i').attr('class', {json_encode($propval)});
            iconpicker.data('iconpicker').setSourceValue({json_encode($propval)});
            iconpicker.data('iconpicker').update();
            iconpicker.show();

            iconpicker.on('iconpickerSelected', e => {
                let faClass = e.iconpickerValue;
                $('#config-{$propname}').val(faClass);
                iconpicker.find('.popover-title i').attr('class', faClass);
            });
        });
    </script>
</div>
