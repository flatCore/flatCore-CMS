<p>{$cnt_items} {$lang_label_cnt_sc_items}</p>

{if $cart_alert_error != ''}
    <div class="alert alert-danger">
        {$cart_alert_error}
    </div>
{/if}

{if $cart_alert_success != ''}
    <div class="alert alert-success">
        {$cart_alert_success}
    </div>
{/if}

{if $cnt_items > 0}
    {include file='shopping_cart_table.tpl'}
{/if}


<script type='text/javascript'>
    $(document).ready(function() {
        $('input[name^=set_payment]').change(function(){
            $('form#set_payment').submit();
        });
    });
</script>