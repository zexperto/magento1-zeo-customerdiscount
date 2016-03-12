<?php
class Zeo_CustomerDiscount_Helper_Data extends Mage_Core_Helper_Abstract
{
	static function getCustomerDiscount(){
		
		$oCustomer=Mage::getSingleton('customer/session')->getCustomer();
		$customer_discount=$oCustomer->getZeoCustomerDiscount();//2.7;
		
		$quote_subtotal=Mage::helper('checkout/cart')->getQuote()->getSubtotal() ;
		$discount_value=$customer_discount*$quote_subtotal/100;
		
		$string_discount="";
		
		if($discount_value>0)
			$string_discount=$customer_discount."% = ".Mage::helper('core')->currency($discount_value, true, false);
		Mage::getSingleton('customer/session')->setZeoCustomerDiscount($string_discount);
		
		return $discount_value;
	}
}
	 