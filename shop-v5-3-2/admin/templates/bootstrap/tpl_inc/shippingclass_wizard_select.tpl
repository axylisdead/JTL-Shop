<div id="innerCombi-{$key}-{$inner}" data-num="{$inner}" class="form-group form-row innerCombi">
    {if $showLogicText}
        <span class="small text-lowercase font-weight-bold d-block w-100 mb-3 innerCombi-logic-text"></span>
    {/if}
    <div class="col col-9">
        <select class="custom-select custom-select-sm" data-num="{$inner}" name="wizard[combi][{$key}][class][{$inner}]">
            {foreach $shippingClasses as $shippingClass}
                <option value="{$shippingClass->kVersandklasse}"{if $class == {$shippingClass->kVersandklasse}} selected{/if}>{$shippingClass->cName|escape}</option>
            {/foreach}
        </select>
    </div>
    <div class="col-auto">
        <button data-num="0" class="btn btn-link p-0 innerCombi-minus"><span class="far fa-trash-alt"></span></button>
    </div>
</div>