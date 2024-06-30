<script src="https://www.paypalobjects.com/webstatic/ppplus/ppplus.min.js" type="text/javascript"></script>    

{if $hinweis}
    <div class="alert alert-danger">{$hinweis}</div>
{/if}

<div class="row">    
    <div class="col-xs-12 col-md-10 col-md-offset-1">
    <div class="well panel-wrap">
        <div class="panel panel-default">
            <div class="panel-heading"><h3 class="panel-title">{lang key="paymentOptions" section="global"}</h3></div>
            <div class="panel-body">
				<form>
					<fieldset>
					{if empty($cFehler)}
						<div class="alert alert-info">{lang key="paymentOptionsDesc" section="shipping payment"}</div>
					{else}
						<div class="alert alert-danger">{$cFehler}</div>
					{/if}
					<div id="pp-plus">
						<div id="ppp-container"></div>
					</div>
					</fieldset>
					<div class="text-right">
						<button type="submit" id="ppp-submit" class="btn btn-primary submit">{lang key="continueOrder" section="account data"}</button>
					</div>
				</form>
            </div>
        </div>
    </div>
    </div>
</div>

<script type="application/javascript">
var submit = 'ppp-submit';
var thirdPartyPayment = false;
var ppp = PAYPAL.apps.PPP({ldelim}
    approvalUrl: "{$approvalUrl}",
    placeholder: "ppp-container",
    mode: "{$mode}",
{if $mode == 'sandbox'}
    showPuiOnSandbox: true,
{/if}
    buttonLocation: "outside",
    disableContinue: submit,
    enableContinue: submit,
    showLoadingIndicator: true,
    language: "{$language}",
    country: "{$country}",
    onThirdPartyPaymentMethodSelected: function(data) {ldelim}
        thirdPartyPayment = true;
    {rdelim},
    onThirdPartyPaymentMethodDeselected: function(data) {ldelim}
        thirdPartyPayment = false;
    {rdelim},
    onContinue: function() {ldelim}
        if (thirdPartyPayment) {ldelim}
            PAYPAL.apps.PPP.doCheckout();
        {rdelim} else {ldelim}
            $('#' + submit).attr('disabled', true);
            $.get("index.php", {ldelim} s: "{$linkId}", a: "payment_patch", id: "{$paymentId}" {rdelim})
                .success(function() {ldelim}
                    PAYPAL.apps.PPP.doCheckout();
                {rdelim})
                .fail(function(res) {ldelim}
                    $('#' + submit).attr('disabled', false);
                    var error = JSON.parse(res.responseText);
                    var errorText = 'Unknown error';
                    if (error && error.message) {
                        errorText = error.message;
                    }
                    alert(errorText);
                {rdelim});
        {rdelim}
    {rdelim},
    {if $thirdPartyPaymentMethods|@count > 0}
        thirdPartyPaymentMethods: {$thirdPartyPaymentMethods|@json_encode}
    {/if}
{rdelim});
$(document).ready(function() {ldelim}
    $('#' + submit).click(function() {ldelim}
        ppp.doContinue();
        return false;
    {rdelim});
{rdelim});
</script>
