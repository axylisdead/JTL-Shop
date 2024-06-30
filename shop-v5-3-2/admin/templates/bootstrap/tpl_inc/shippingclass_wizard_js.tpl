<div class="modal fade" id="shippingMethodWizard" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title pt-2 pb-2" id="myModalLabel">{__('labelShippingClassWizard')}</h3>
                <a href="{__('https://jtl-url.de/dlkpz')}" target="_blank" class="btn btn-link btn-lg" data-toggle="tooltip" data-container="body" data-placement="left" title="" data-original-title="{__('Zur Dokumentation')}">
                    <span class="fal fa-map-signs"></span>
                </a>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <span class="file-loading"></span>
            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="ml-auto col-sm-6 col-lg-auto mb-2">
                        <button data-dismiss="modal" class="btn btn-outline-primary btn-block"><i class="fas fa-exclamation"></i> {__('Cancel')}</button>
                    </div>
                    <div class="col-sm-6 col-lg-auto">
                        <button id="shippingMethodWizardSave" class="btn btn-primary btn-block"><i class="fal fa-save"></i> {__('apply')}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="application/javascript">
    {literal}
    let wizardModalJs = null
    new function() {
        let adminPath        = {/literal}'{$smarty.const.PFAD_ADMIN}'{literal},
            shippingMethodID = {/literal}'{if isset($Versandart)}{$Versandart->kVersandart}{else}0{/if}'{literal},
            suppressWarning  = false;

        function wizard_showShippingMethodsList(jsonList) {
            $btnAddCombi.addClass('btn-outline-primary').removeClass('btn-primary');
            $btnEdit.addClass('btn-primary').removeClass('btn-outline-primary');
            $('.input-group', $smContainer).not('#liVKneu').remove();
            if (jsonList.length) {
                /*let $list = $('<ul class="list-unstyled list-inline"></ul>');*/
                for (let i = 0; i < jsonList.length; i++) {
                    let $newCombi = addShippingCombination(),
                        itemList  = jsonList[i].split(', ');
                    $('option', $newCombi).filter(function (pos, elem) {
                        return itemList.indexOf(elem.textContent) >= 0;
                    }).attr('selected', 'selected');
                }
                $('.select2').select2();
            }
        }

        function wizard_saveShippingClasses() {
            let formData    = $('#shippingMethodWizard form[name="wizard"]').serialize();
            suppressWarning = true;
            disableButton($btnSave);
            ioManagedCall(adminPath, 'wizardShippingMethodCreate', {formData:formData}, function (result, error) {
                let $modal = $('#shippingMethodWizard');
                $modal.modal('hide');

                if (result && !error) {
                    smIsWizzard = true;
                    $smInput.val(result.shippingMethods);
                    $smDefInput.val(result.definition);
                    $smHashInput.val(result.resultHash);
                    wizard_showShippingMethodsList(JSON.parse(result.wizardJsonSM));
                } else {
                    createNotify(
                        {title: 'Fehler', message: error.message},
                        {type: 'danger'}
                    );
                }
            });
        }

        function disableButton($btn, wait = true) {
            $btn.attr('disabled', 'disabled');
            if (wait) {
                $(':first-child', $btn).addClass('spinner-grow spinner-grow-sm');
            } else {
                $(':first-child', $btn).removeClass('spinner-grow spinner-grow-sm');
            }
        }

        function enableButton($btn) {
            $btn.attr('disabled', false);
            $(':first-child', $btn).removeClass('spinner-grow spinner-grow-sm');
        }

        let smJSON        = {/literal}{$wizardJsonShippingMethods}{literal},
            smIsWizzard   = {/literal}{if $isWizardDefinition}true{else}false{/if}{literal},
            $smContainer  = $('#ulVK'),
            $smInput      = $('input[name="kVersandklasse"]'),
            $smDefInput   = $('input[name="wizardShippingMethodDefinition"]'),
            $smHashInput  = $('input[name="wizardShippingMethodHash"]'),
            $btnAddCombi  = $('#addNewShippingClassCombi'),
            $btnSave      = $('#shippingMethodWizardSave'),
            $btnEdit      = $('#editShippingClassCombi');

        $btnEdit.on('click', function () {
            let $modal = $('#shippingMethodWizard .modal-body');
            $modal.empty().append($('<span class="file-loading"></span>'));
            ioManagedCall(adminPath, 'wizardShippingMethod', {
                id:parseInt(shippingMethodID),
                shippingClassIds:$smInput.val(),
                definition:$smDefInput.val(),
                suppressWarning:suppressWarning
            }, function (result, error) {
                $modal.empty();
                if (result && !error) {
                    $modal.append($(result));
                    enableButton($btnSave);
                    if (wizardModalJs !== null) {
                        wizardModalJs.initWizard();
                    }
                } else {
                    disableButton($btnSave, false);
                    $modal.append($('<div class="alert alert-danger">' + error.message + '</div>'));
                }
            });
        });
        $("input[name='kVersandklasse']").on('change', function (e) {
            suppressWarning = false;
            $btnEdit.addClass('btn-outline-primary').removeClass('btn-primary');
            $btnAddCombi.addClass('btn-primary').removeClass('btn-outline-primary');
        })
        $btnSave.on('click', function () {
            wizard_saveShippingClasses();
        });

        if (smIsWizzard) {
            wizard_showShippingMethodsList(smJSON);
        }
    }();
    {/literal}
</script>