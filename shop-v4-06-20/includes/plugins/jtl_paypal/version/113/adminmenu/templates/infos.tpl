<div class="container-fluid">
    {if isset($errorMessage) && $errorMessage|@count_characters > 0}
        <div class="alert alert-danger">
            <i class="fa fa-exclamation-triangle"></i> {$errorMessage}
        </div>
    {/if}

    <h2>Konfiguration</h2>

    <table class="table" id="paypal-test-credentials">
        <thead>
            <tr>
                <th>Zahlungsart</th>
                <th class="text-center">Modus</th>
                <th class="text-center">Zugangsdaten</th>
                <th class="text-center">Verkn&uuml;pft<br><small class="text-muted">Mit Versandart</small></th>
                <th class="text-center"></th>
            </tr>
        </thead>
        <tbody>
            <tr class="basic">
                <td>Basic</td>
                <td class="payment-modus text-center"><i class="fa fa-spinner fa-spin"></i></td>
                <td class="payment-state text-center">
                    <i class="fa fa-spinner fa-spin"></i>
                </td>
                <td class="payment-linked text-center"><i class="fa fa-spinner fa-spin"></i></td>
                <td class="payment-co text-center"></td>
            </tr>
            <tr class="express">
                <td>Express</td>
                <td class="payment-modus text-center"><i class="fa fa-spinner fa-spin"></i></td>
                <td class="payment-state text-center">
                    <i class="fa fa-spinner fa-spin"></i>
                </td>
                <td class="payment-linked text-center"><i class="fa fa-spinner fa-spin"></i></td>
                <td class="payment-co text-center"></td>
            </tr>
            <tr class="plus">
                <td>PLUS</td>
                <td class="payment-modus text-center"><i class="fa fa-spinner fa-spin"></i></td>
                <td class="payment-state text-center"><i class="fa fa-spinner fa-spin"></i></td>
                <td class="payment-linked text-center"><i class="fa fa-spinner fa-spin"></i></td>
                <td class="payment-co text-center"></td>
            </tr>
        </tbody>
    </table>
    <br />

    <p>
        <a href="http://jtl-url.de/paypaldocs" class="btn btn-primary" target="_blank"><i class="fa fa-file-pdf-o"></i> Integrationshandbuch zu diesem Plugin lesen</a>
    </p>
</div>

<script type="text/javascript">
    var url = '{$post_url}';
    var payments = ['basic', 'express', 'plus'];

    $(payments).each(function(i, item) {
        check_payment(item);
    });

    function check_payment(type) {
        $.ajax({
            dataType: "json",
            url: url+'&validate=' + type,
            success: function(data) {
                var error = data.msg || 'Ung&uuml;ltig';

                var label_type = data['status'] == 'success' ? 'G&uuml;ltig' : error;
                var class_state = data['status'] == 'success' ? 'success' : 'danger';
                var state = '<small class="label label-'+class_state+'">'+label_type+'</small>';

                var label_linked = data['linked'] ? 'Ja' : 'Nein';
                var class_linked = data['linked'] ? 'success' : 'danger';
                var linked = '<small class="label label-'+class_linked+'">'+label_linked+'</small>';

                var modus = '<small class="label label-info">'+data['modus'].toUpperCase()+'</small>';
                var co    = '';
                if (typeof data['coCorrect'] !== "undefined") {
                    co = data['coCorrect'] ? '' : '<i class="fa fa-exclamation-triangle text-danger" data-toggle="tooltip" title="' + data['coMsg'] + '"></i>';
                    if (data['coLink']) {
                        co = '<a href="' + data['coLink'] + '">' + co + '</a>';
                    }
                }

                $('tr.' + data['type'] + ' td.payment-state').html(state);
                $('tr.' + data['type'] + ' td.payment-linked').html(linked); 
                $('tr.' + data['type'] + ' td.payment-modus').html(modus);
                $('tr.' + data['type'] + ' td.payment-co').html(co);
            }
        });
    }
</script>