<form method="post" enctype="multipart/form-data" name="wizard">
    {$jtl_token}
    {if isset($wizardDescription)}
        {foreach $wizardDescription as $description}
            <div class="description alert alert-warning">{$description}</div>
        {/foreach}
    {/if}
    <div class="card mb-0">
        <div class="card-header">
            <span class="subheading1">{if isset($shippingMethod)}{$shippingMethod->cName}{else}{__('New shipping method')}{/if}</span>
            <input type="hidden" name="kVersandart" value="{if isset($shippingMethod)}{$shippingMethod->kVersandart}{else}0{/if}">
            <hr class="mb-n3">
        </div>
        <div id="wizard" class="card-body pb-0 pt-0">
            {assign var='numOuterCombi' value=0}
            {assign var='numInnerCombi' value=0}
            <label for="wizard_combinations_showing" class="font-weight-bold d-block mb-3">
                    {__('wizardShowingCombinations')}
            </label>
            <div class="form-group form-row align-items-center">
                <div class="col col-11">
                    <select id="wizard_combinations_showing" class="custom-select" name="wizard[show]">
                        <option value="ever"{if $wizard->mapShowing() === 'ever'} selected{/if}>
                            {__('wizardShowingCombinationsAllways')}
                        </option>
                        <option value="show"{if $wizard->mapShowing() === 'show'} selected{/if}>
                            {__('wizardShowingCombinationsShow')}
                        </option>
                        <option value="not"{if $wizard->mapShowing() === 'not'} selected{/if}>
                            {__('wizardShowingCombinationsHide')}
                        </option>
                    </select>
                </div>
                <div class="col col-1 text-right" data-html="true" data-toggle="tooltip" data-placement="left" title=""
                      data-original-title="{__('wizard_combinations_showing_tooltip')}">
                    <span class="fas fa-info-circle fa-fw"></span>
                </div>
            </div>
            <div class="form-group form-row align-items-center collapse fade dependWizardShow">
                <div class="col col-11">
                    <select id="wizard_combinations_condition" class="custom-select" name="wizard[condition]">
                        <option value="or"{if $wizard->mapCondition() === 'or'} selected{/if}>
                            {__('wizardCombinationsConditionOr')}
                        </option>
                        <option value="xor"{if $wizard->mapCondition() === 'xor'} selected{/if}>
                            {__('wizardCombinationsConditionXOr')}
                        </option>
                        <option value="and"{if $wizard->mapCondition() === 'and'} selected{/if}>
                            {__('wizardCombinationsConditionAnd')}
                        </option>
                    </select>
                </div>
                <div class="col col-1 text-right" data-html="true" data-toggle="tooltip" data-placement="left" title=""
                      data-original-title="{__('wizard_combinations_condition_tooltip')}">
                    <span class="fas fa-info-circle fa-fw"></span>
                </div>
            </div>
            <div id="allCombi" class="allCombination collapse fade dependWizardShow">
                {if $wizard->hasDefinitionParts()}
                    {foreach $wizard->getDefinitionParts() as $key => $combi}
                    {assign var="groupNumber" value=$key + 1}
                    <div id="outerCombi-{$key}">
                        <div class="border border-bottom-0 p-2 alert-info small outerCombi-head">
                            {__('Condition', $groupNumber)}
                        </div>
                        <div class="card-body border mb-3 pb-0 pt-2 outerCombi" data-num="{$key}">
                            <span class="small font-weight-bold mb-3 d-block w-100 innerCombi-logic-text"></span>
                            {foreach $combi->getShippingClasses() as $inner => $class}
                                {include file="tpl_inc/shippingclass_wizard_select.tpl" key=$key inner=$inner class=$class shippingClasses=$shippingClasses showLogicText=false}
                            {/foreach}
                            <div class="form-group form-row">
                                <div class="col-auto">
                                    <button data-num="0" class="btn btn-outline-primary innerCombi-plus">
                                        <i class="fas fa-plus-circle fa-fw"></i>
                                        {__('addShippincCass')}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    {/foreach}
                {else}
                    <div id="outerCombi-0">
                        <div class="border border-bottom-0 p-2 alert-info small">
                            {__('Condition', 1)}
                        </div>
                        <div class="card-body border mb-3 pb-0 pt-2 outerCombi" data-num="0">
                            <span class="small font-weight-bold mb-3 d-block w-100 innerCombi-logic-text"></span>
                            {include file="tpl_inc/shippingclass_wizard_select.tpl" key=0 inner=0 class=0 shippingClasses=$shippingClasses showLogicText=false}
                            <div class="form-group form-row">
                                <div class="col-auto">
                                    <button data-num="0" class="btn btn-outline-primary innerCombi-plus">
                                        <i class="fas fa-plus-circle fa-fw"></i>
                                        {__('addShippincCass')}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                {/if}
                <div class="form-group form-row">
                    <div class="col-auto">
                        <button data-num="0" class="btn btn-outline-primary outerCombi-plus">
                            <i class="fas fa-plus-circle fa-fw"></i>
                            {__('addCondition')}
                        </button>
                    </div>
                </div>
            </div>
            <div class="form-group form-row align-items-center collapse fade dependWizardShow dependWizardCond">
                <div class="col col-11">
                    <select id="wizard_combinations_exclusive" class="custom-select" name="wizard[exclusive]">
                        <option value="inclusive"{if $wizard->mapExclusive() === 'inclusive'} selected{/if}>
                            {__('wizardCombinationsConditionInclusive')}
                        </option>
                        <option value="exclusive"{if $wizard->mapExclusive() !== 'inclusive'} selected{/if}>
                            {__('wizardCombinationsConditionExclusive')}
                        </option>
                    </select>
                </div>
                <div class="col col-1 text-right" data-html="true" data-toggle="tooltip" data-placement="left" title=""
                      data-original-title="{__('wizard_combinations_exclusive_tooltip')}">
                    <span class="fas fa-info-circle fa-fw"></span>
                </div>
            </div>
        </div>
    </div>
</form>
<div id="innerCombi-blueprint" class="collapse">
    {include file="tpl_inc/shippingclass_wizard_select.tpl" key="#" inner="#" class=0 shippingClasses=$shippingClasses showLogicText=false}
</div>
<div id="outerCombi-blueprint" class="collapse">
    <div>
        <div class="border border-bottom-0 p-2 alert-info small outerCombi-head"></div>
        <div class="card-body border mb-3 pb-0 pt-2 outerCombi">
            <span class="small font-weight-bold mb-3 d-block w-100 innerCombi-logic-text"></span>
            <div class="form-group form-row">
                <div class="col-auto">
                    <button data-num="0" class="btn btn-outline-primary innerCombi-plus">
                        <i class="fas fa-plus-circle fa-fw"></i>
                        {__('addShippincCass')}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="application/javascript">
    wizardModalJs = {
        wizardShow: 'ever',
        wizardCond: 'and',
        wizardPartsLogicText: '',
        $wizard: $('#wizard'),

        handleShowState: function () {
            this.wizardShow = $('#wizard_combinations_showing').val();
            if (this.wizardShow === 'ever') {
                $('.dependWizardShow').removeClass('show');
            } else {
                $('.dependWizardShow').addClass('show');
            }
        },

        handleConditionState: function () {
            this.wizardCond = $('#wizard_combinations_condition').val();
            if (this.wizardCond === 'xor') {
                $('#wizard_combinations_exclusive').val('exclusive').attr('disabled', true);
                this.wizardPartsLogicText = '{__('PartsLogicOr')}';
            } else if (this.wizardCond === 'or') {
                $('#wizard_combinations_exclusive').attr('disabled', false);
                this.wizardPartsLogicText = '{__('PartsLogicOr')}';
            } else {
                $('#wizard_combinations_exclusive').attr('disabled', false);
                this.wizardPartsLogicText = '{__('PartsLogicAnd')}';
            }
            $('.innerCombi-logic-text').text(this.wizardPartsLogicText);
        },

        handleConditionNames: function () {
            $('.outerCombi-head', this.$wizard).each(function (item) {
                $(this).text('{__('Condition')}'.replace('%d', item + 1));
            });
        },

        createInnerCombi: function (o, i) {
            let $innerCombi = $($('#innerCombi-blueprint').html());
            $innerCombi
                .attr('id', 'innerCombi-' + o + '-' + i)
                .attr('data-num', i);
            $('select', $innerCombi)
                .attr('data-num', i)
                .attr('name', 'wizard[combi][' + o + '][class][' + i + ']');

            return $innerCombi;
        },

        addInnerCombi: function ($target) {
            let $outerCombi = $target.closest('.outerCombi'),
                i           = $outerCombi.data('countInnerCombis'),
                o           = $outerCombi.attr('data-num'),
                $formGroup  = $target.closest('.form-group'),
                $newInner   = this.createInnerCombi(o, i);

            $formGroup.before($newInner);
            $outerCombi.data('countInnerCombis', ++i);
        },

        addInnerCombiCallback: function (ev) {
            ev.preventDefault();
            this.addInnerCombi($(ev.target));
        },

        removeInnerCombi: function ($target) {
            let $outerCombi = $target.closest('.outerCombi');

            $target.closest('.innerCombi').remove();
            if ($('.innerCombi', $outerCombi).length === 0) {
                $outerCombi.parent().remove();
                $outerCombi.data('countInnerCombis', $('.outerCombi', this.$wizard).length);
                this.handleConditionNames();
            }
            $('.innerCombi:first', $outerCombi).find('.innerCombi-logic-text').remove();
        },

        removeInnerCombiCallback: function (ev) {
            ev.preventDefault();
            this.removeInnerCombi($(ev.target))
        },

        createOuterCombi: function (i) {
            let $outerCombi = $($('#outerCombi-blueprint').html());
            $outerCombi.attr('id', 'outerCombi-' + i);
            $('.outerCombi', $outerCombi)
                .attr('data-num', i)
                .data('countInnerCombis', 0);
            $('select', $outerCombi)
                .attr('data-num', i)
                .attr('name', 'wizard[combi][' + i + '][logic]');

            return $outerCombi;
        },

        addOuterCombi: function($target) {
            let $allCombi = $('#allCombi'),
                i         = $allCombi.data('countOuterCombis'),
                $newOuter = this.createOuterCombi(i);


            $target.closest('.form-group').before($newOuter);
            this.addInnerCombi($('button', $newOuter));
            $allCombi.data('countOuterCombis', ++i);
            this.handleConditionNames();
        },

        addOuterCombiCallback: function (ev) {
            ev.preventDefault();
            this.addOuterCombi($(ev.target));
        },

        initWizard: function () {
            $('#allCombi').data('countOuterCombis', $('.outerCombi', this.$wizard).length);
            $('.outerCombi', this.$wizard).each(function (idx, el) {
                let $el = $(el);

                $el.data('countInnerCombis', $('.innerCombi', $el).length);
            });
            this.handleShowState();
            this.handleConditionState();
            $('#wizard_combinations_showing').on('change', this.handleShowState);
            $('#wizard_combinations_condition').on('change', this.handleConditionState);
            this.$wizard.on('click', '.innerCombi-plus', this.addInnerCombiCallback.bind(this));
            this.$wizard.on('click', '.innerCombi-minus', this.removeInnerCombiCallback.bind(this));
            this.$wizard.on('click', '.outerCombi-plus', this.addOuterCombiCallback.bind(this));
        }
    };
</script>