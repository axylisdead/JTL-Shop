{if !isset($cParam_arr)}
    {assign var=cParam_arr value=[]}
{/if}

<div class="toolbar">
    <form method="get">
        {$jtl_token}
        {foreach $cParam_arr as $cParamName => $cParamValue}
            <input type="hidden" name="{$cParamName}" value="{$cParamValue}">
        {/foreach}
        <div class="form-row align-items-end">
            {foreach $oFilter->getFields() as $field}
                {if $field->getType() === 'text'}
                    {if $field->isCustomTestOp()}
                        <div class="col-md-6 col-lg-3 col-xl-2">
                            <div class="form-group">
                                <label for="{$oFilter->getId()}_{$field->getId()}">{$field->getTitle()}:</label>
                                <select class="custom-select"
                                        name="{$oFilter->getId()}_{$field->getId()}_op"
                                        id="{$oFilter->getId()}_{$field->getId()}_op">
                                    {if $field->getDataType() == 0}
                                        <option value="1"{if $field->getTestOp() == 1} selected{/if}>{__('contains')}</option>
                                        <option value="2"{if $field->getTestOp() == 2} selected{/if}>{__('startsWith')}</option>
                                        <option value="3"{if $field->getTestOp() == 3} selected{/if}>{__('endsWith')}</option>
                                        <option value="4"{if $field->getTestOp() == 4} selected{/if}>{__('isEqual')}</option>
                                        <option value="9"{if $field->getTestOp() == 9} selected{/if}>{__('isNotEqual')}</option>
                                    {elseif $field->getDataType() == 1}
                                        <option value="4"{if $field->getTestOp() == 4} selected{/if}>=</option>
                                        <option value="9"{if $field->getTestOp() == 9} selected{/if}>!=</option>
                                        <option value="5"{if $field->getTestOp() == 5} selected{/if}>&lt;</option>
                                        <option value="6"{if $field->getTestOp() == 6} selected{/if}>&gt;</option>
                                        <option value="7"{if $field->getTestOp() == 7} selected{/if}>&lt;=</option>
                                        <option value="8"{if $field->getTestOp() == 8} selected{/if}>&gt;=</option>
                                    {/if}
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3 col-xl-2">
                            <div class="form-group">
                    {else}
                        <div class="col-md-6 col-lg-3 col-xl-2">
                            <div class="form-group">
                                <label for="{$oFilter->getId()}_{$field->getId()}">{$field->getTitle()}:</label>
                    {/if}
                                <input type="{if $field->getDataType() == 1}number{else}text{/if}"
                                       class="form-control" name="{$oFilter->getId()}_{$field->getId()}"
                                       id="{$oFilter->getId()}_{$field->getId()}"
                                       value="{$field->getValue()}" placeholder="{$field->getTitle()}"
                                       {if $field->getTitleLong() !== ''}data-toggle="tooltip"
                                       data-placement="bottom" title="{$field->getTitleLong()}"{/if}>
                            </div>
                        </div>
                {elseif $field->getType() === 'select'}
                    <div class="col-md-6 col-lg-3 col-xl-2">
                        <div class="form-group">
                            <label for="{$oFilter->getId()}_{$field->getId()}">{$field->getTitle()}:</label>
                            <select class="custom-select"
                                    name="{$oFilter->getId()}_{$field->getId()}"
                                    id="{$oFilter->getId()}_{$field->getId()}"
                                    {if $field->getTitleLong() !== ''}data-toggle="tooltip"
                                    data-placement="bottom" title="{$field->getTitleLong()}"{/if}
                                    {if $field->reloadOnChange}onchange="$('#{$oFilter->getId()}_btn_filter').click()"{/if}>
                                {foreach $field->getOptions() as $i => $oOption}
                                    <option value="{$i}"{if $i == (int)$field->getValue()} selected{/if}>
                                        {$oOption->getTitle()}
                                    </option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                {elseif $field->getType() === 'daterange'}
                    <div class="col-md-6 col-lg-3 col-xl-2">
                        <div class="form-group">
                            <label for="{$oFilter->getId()}_{$field->getId()}">{__($field->getTitle())}:</label>
                            <input type="text"  class="form-control"
                                   name="{$oFilter->getId()}_{$field->getId()}"
                                   id="{$oFilter->getId()}_{$field->getId()}">
                            {include
                                file="snippets/daterange_picker.tpl"
                                datepickerID="#{$oFilter->getId()}_{$field->getId()}"
                                currentDate="{$field->getValue()}"
                                format="DD.MM.YYYY"
                                separator="{__('datepickerSeparator')}"
                            }
                        </div>
                    </div>
                {/if}
            {/foreach}
            <div class="col-md-auto">
                <div class="form-group mt-2">
                    <button type="submit" class="btn btn-primary btn-block" name="action" value="{$oFilter->getId()}_filter"
                            title="{__('useFilter')}" id="{$oFilter->getId()}_btn_filter">
                        <i class="fal fa-search"></i>
                    </button>
                </div>
            </div>
            <div class="col-md-auto">
                <div class="form-group mt-2">
                    <button type="submit" class="btn btn-outline-primary btn-block" name="action" value="{$oFilter->getId()}_resetfilter"
                            title="{__('resetFilter')}" id="{$oFilter->getId()}_btn_resetfilter">
                        <i class="fa fa-eraser"></i>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
