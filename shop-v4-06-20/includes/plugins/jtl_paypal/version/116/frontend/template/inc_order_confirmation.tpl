<div class="modal modal-center fade" id="ppp-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <h2 id="pp-loading-body"><i class="fa fa-spinner fa-spin fa-fw"></i> Ihre Bestellung wird bearbeitet</h2>
            </div>
        </div>
    </div>
</div>

<script>
$(function() {
    var submitted = false;

    $('#complete_order').on('submit', function() {
        submitted = true;

        $(this).find('input[type="submit"]')
            .addClass('disabled')
            .attr('disabled', true);

        $('#ppp-modal').modal({
            backdrop: 'static'
        });

        $('#ppp-modal').modal('show');

        return true;
    });
    {if isset($pp_psd2overcharge) && $pp_psd2overcharge}
    eModal.setModalOptions({
        backdrop: 'static',
    })
    eModal.alert({
        message: '{$pp_psd2overcharge_desc}',
        title: '{$pp_psd2overcharge_title}',
        buttons: [{
            close: true,
            text: 'OK',
            click: function () {
                let $form     = $('#complete_order'),
                    $required = $('input[required]', $form),
                    $ppModal  = $('#ppp-modal'),
                    link      = '{$pp_psd2overcharge_link}';
                if (link !== '') {
                    $ppModal.modal({
                        backdrop: 'static'
                    });
                    $ppModal.modal('show');
                    window.location.href = link;
                } else if ($required.length === 0) {
                    $form.submit();
                }
            }
        }],
    });
    {/if}
});
</script>
