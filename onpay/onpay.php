<?php

class OnPay extends PaymentModule
{
	private $_html = '';
	protected $supported_currencies = array('RUB', 'EUR', 'USD');

	public function __construct()
	{	// Параметры установки
		$this->name = 'onpay';
		$this->tab = 'payments_gateways';
		$this->version = '1.5';
		$this->author = 'prestashop-planet.org';
		$this->need_instance = 0;

		$this->currencies = true;
		$this->currencies_mode = 'checkbox';

		parent::__construct();

		$this->_errors = array();
		$this->page = basename(__FILE__, '.php');
        $this->displayName = 'Прием платежей Onpay.ru';
        $this->description = $this->l('Описание : Бесплатный платежный модуль Onpay.ru для интернет-магазинов на основе CMS PrestaShop позволяет принимать Яндекс.Деньги, WebMoney, пластиковые карты VISA и MasterCard, а также множество других интернет-валют.');
		$this->confirmUninstall = $this->l('Удалить модуль и данные onpay?');
		
		if (!Configuration::get('ONPAY_LOGIN'))
			$this->warning = $this->l('Добавьте логин');
		if (!Configuration::get('ONPAY_API_IN_KEY'))
			$this->warning = $this->l('Добавьте ключ API');
		if (!Configuration::get('ONPAY_FORM'))
			$this->warning = $this->l('Добавьте форму (7)');
	}

	public function install()
	{	//установка
		if (!parent::install()
			OR !$this->registerHook('payment')
			OR !$this->registerHook('paymentReturn'))
			return false;
		//установка значений
		// Запись логина ключа и № формы в конфиг
		Configuration::updateValue('ONPAY_LOGIN', '');
		Configuration::updateValue('ONPAY_API_IN_KEY', '');
		Configuration::updateValue('ONPAY_FORM', 7);

		return true;
	}

	public function uninstall()
	{	// удаление логина ключа и № формы в конфиг
		Configuration::deleteByName('ONPAY_LOGIN');
		Configuration::deleteByName('ONPAY_API_IN_KEY');
		Configuration::deleteByName('ONPAY_FORM');

		return parent::uninstall();
	}

	public function getContent()
	{
		if (Tools::isSubmit('submit'))
		{	// Запись логина ключа и № формы в конфиг из формы в админке
			Configuration::updateValue('ONPAY_LOGIN', Tools::getValue('ONPAY_LOGIN'));
			Configuration::updateValue('ONPAY_API_IN_KEY', Tools::getValue('ONPAY_API_IN_KEY'));
			Configuration::updateValue('ONPAY_FORM', Tools::getValue('ONPAY_FORM'));
		}
		//код отображения в админке
		return '
		<form action="'.Tools::htmlentitiesUTF8($_SERVER['REQUEST_URI']).'" method="post">
		<fieldset style="width: 300px;float:right;margin-left:15px;">
	<legend><img src="../img/admin/manufacturers.gif" /> ' . $this->l('Информация') . '</legend>
	<div id="dev_div">
		<span><b>' . $this->l('Версия') . ':</b> ' . $this->version . '</span><br>
		<span><b>' . $this->l('Лицензия') . ':</b> <a class="link" href="http://www.opensource.org/licenses/osl-3.0.php" target="_blank">OSL 3.0</a></span><br>
		<span><b>' . $this->l('Разработчик') . ':</b> <a class="link" href="mailto:mbpresta@rambler.ru" target="_blank">psstore.org</a><br>
                <span><b>' . $this->l('Обсудить') . ':</b> <a class="link" href="http://prestashop-planet.org/" target="_blank">prestashop-planet.org</a><br>
	</div>
</fieldset>
			<fieldset><legend><img src="../modules/'.$this->name.'/logo.gif" /> '.$this->l('Settings').'</legend>
				<label for="wmr">'.$this->l('LOGIN:').'</label>
				<div class="margin-form">
					<input type="text" size="25" maxlength="13" name="ONPAY_LOGIN" value="'.Tools::getValue('ONPAY_LOGIN', Configuration::get('ONPAY_LOGIN')).'" />
				</div>
				<div class="clear">&nbsp;</div>
				<label for="wmz">'.$this->l('API IN KEY:').'</label>
				<div class="margin-form">
					<input type="text" size="25" maxlength="13" name="ONPAY_API_IN_KEY" value="'.Tools::getValue('ONPAY_API_IN_KEY', Configuration::get('ONPAY_API_IN_KEY')).'" />
				</div>
				<div class="clear">&nbsp;</div>
				<label for="key">'.$this->l('ONPAY FORM:').'</label>
				<div class="margin-form">
					<input type="text" size="2" name="ONPAY_FORM" value="'.Tools::getValue('ONPAY_FORM', Configuration::get('ONPAY_FORM')).'" />
				</div>
				<div class="clear">&nbsp;</div>
				<label for="key">'.$this->l('URL API(не изменяется)').'</label>
				<div class="margin-form">
					<input type="text" size="75" name="ap" value="http://'.$_SERVER['HTTP_HOST'].$this->_path.'validation.php" />
				</div>
				<div class="clear">&nbsp;</div>
				<center><input type="submit" name="submit" value="'.$this->l('Update settings').'" class="button" /></center>
			</fieldset>
		</form>
		
		<div class="clear">&nbsp;</div>';
	}

	public function hookPayment($params)
	{
		global $smarty;
		$currency = new Currency((int)($params['cart']->id_currency));//получение данные о валюте

		$smarty->assign(array(
			'id' => (int)$params['cart']->id,//отдаём id корзины
			'this_path' => $this->_path//отдаём адрес папки ис модулем
		));

		return $this->display(__FILE__, 'payment.tpl');//шаблон в который отдавали
    }		
}
?>