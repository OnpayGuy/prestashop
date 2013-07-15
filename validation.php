<?php
//подключаем всё что нужно
include(dirname(__FILE__). '/../../config/config.inc.php');
include(dirname(__FILE__).'/onpay.php');

$onpay = new OnPay();// создаём объект

if ($_REQUEST['type'] == 'check')//если проверка
{  //делаем md5 в верхнем регистре
	$md5 = strtoupper(md5($_REQUEST['type'].';'.$_REQUEST['pay_for'].';'.$_REQUEST['order_amount'].';'.$_REQUEST['order_currency'].';'.'0'.';'.Configuration::get('ONPAY_API_IN_KEY')));
	echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<result>\n<code>0</code>\n<pay_for>".$_REQUEST['pay_for']."</pay_for>\n<comment>OK</comment>\n<md5>$md5</md5>\n</result>";
}

if ($_REQUEST['type'] == 'pay')
{
	$cart = new Cart(intval($_REQUEST['pay_for']));// объект Cart(получение инфы о заказе)
	$currency = new Currency(intval($cart->id_currency));//объект валют(получение инфы о валюте)
	$customer = new Customer((int)$cart->id_customer);//объект пользователи(получение инфы о пользователе)
	$amount = number_format($cart->getOrderTotal(true, Cart::BOTH), 1, '.', '');//цена заказа
	$order = New Order();// Объект заказ
	$id_order = $order->getOrderByCartId($_REQUEST['pay_for']);//Получение id заказа черекз id корзины от onpay
	//print_r($id_order);
			
	if ($_REQUEST['order_amount'] == $amount)//если цена запроса и заказа равны
	{	//делаем md5 в верхнем регистре
		$md5 = strtoupper(md5($_REQUEST['type'].';'.$_REQUEST['pay_for'].';'.$_REQUEST['onpay_id'].';'.$_REQUEST['pay_for'].';'.$_REQUEST['order_amount'].';'.$_REQUEST['order_currency'].';'.'0'.';'.Configuration::get('ONPAY_API_IN_KEY')));
		//подтверждаем заказ в магазине и сохраняем
			$history = new OrderHistory();// Объект История заказов
			$history->id_order = $id_order;//Получение данных о заказе через id заказа
			$history->changeIdOrderState(_PS_OS_PAYMENT_, $history->id_order);//Изменим статус заказа на "Оплачен"
		echo  "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<result>\n<code>0</code>\n <comment>OK</comment>\n<onpay_id>".$_REQUEST['onpay_id']."</onpay_id>\n <pay_for>".$_REQUEST['pay_for']."</pay_for>\n<order_id>".$_REQUEST['pay_for']."</order_id>\n<md5>$md5</md5>\n</result>";
	}
}
