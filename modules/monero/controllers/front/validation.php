<?php
//include(dirname(__FILE__). '/../../library.php'); This will be used later to connect to the wallet-rpc
class moneroValidationModuleFrontController extends ModuleFrontController
{
	public function postProcess()
	{
		global $currency;
        $cart = $this->context->cart;
      	$c = $currency->iso_code;
		$total = $cart->getOrderTotal();
		$amount = $this->changeto($total, $c);
		$actual = $this->retriveprice($c);
		$address = Configuration::get('MONERO_ADDRESS');
		
		$this->context->smarty->assign(array(
            'this_path_ssl'   => Tools::getShopDomainSsl(true, true) . __PS_BASE_URI__ . 'modules/' . $this->module->name . '/',
				'address' => $address,
				'amount' => $amount ));
		$this->setTemplate('payment_box.tpl');
	}
	
	public function retriveprice($c)
	{
		$xmr_price = Tools::file_get_contents('https://min-api.cryptocompare.com/data/price?fsym=XMR&tsyms=BTC,USD,EUR,CAD,INR,GBP&extraParams=monero_woocommerce');
		$price         = json_decode($xmr_price, TRUE);
							
		if ($c== 'USD') {
			return $price['USD'];
		}
		if ($c== 'EUR') {
			return $price['EUR'];
		}
		if ($c== 'CAD'){
			return $price['CAD'];
		}
		if ($c== 'GBP'){
			return $price['GBP'];
		}
		if ($c== 'INR'){
			return $price['INR'];
		}
		else{
			//return $price['USD'];
		}
	}
				
	public function changeto($amount, $currency)
	{
		$xmr_live_price = $this->retriveprice($currency);
		$new_amount     = $amount / $xmr_live_price;
		$rounded_amount = round($new_amount, 12); //the moneo wallet can't handle decimals smaller than 0.000000000001
		return $rounded_amount;
	}
}
