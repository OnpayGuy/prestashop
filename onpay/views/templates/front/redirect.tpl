{l s='Ожидание перенаправления' mod='onpay'}

<form id="onpay" method="GET" action="http://secure.onpay.ru/pay/{$login}">
	<input type="hidden" name="pay_mode" value="fix" />
	<input type="hidden" name="f" value="{$f}" />
	<input type="hidden" name="pay_for" value="{$id}" />
	<input type="hidden" name="price" value="{$price}" />
	<input type="hidden" name="currency" value="{$currency}" />
	<input type="hidden" name="md5" value="{$md5}" />
	<input type="hidden" name="url_success" value="{$url_success}" />
	 <input type="submit" value="{l s='Оплатить' mod='onpay'}">
</form>

<script type="text/javascript">
	$('#onpay').submit();
</script>