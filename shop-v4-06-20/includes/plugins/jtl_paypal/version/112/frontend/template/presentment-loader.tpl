<div class="ppf-loader">
    <script>
        $(function() {
            jtl_paypal({ selector: '.ppf-loader' })
                .getInstallments('{$amount}', '{$currency}');
        });
    </script>
</div>