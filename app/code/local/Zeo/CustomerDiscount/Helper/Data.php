<?php
class Zeo_CustomerDiscount_Helper_Data extends Mage_Core_Helper_Abstract
{
    static function getApplyDiscountonTax(){
    
        $apply_tax_discount = Mage::getStoreConfig("zeo_customerdiscount_setting/general/apply_tax_discount");
        return  $apply_tax_discount;
    }
	static function getCustomerDiscount($percent = false){
		
		$oCustomer=Mage::getSingleton('customer/session')->getCustomer();
		$customer_discount = $oCustomer->getZeoCustomerDiscount();//2.7;
		
		$quote_subtotal = Mage::helper('checkout/cart')->getQuote()->getSubtotal() ;
		$discount_value = $customer_discount*$quote_subtotal/100;
		
		$string_discount = "";
		
		if($discount_value > 0){
			$string_discount = $customer_discount."% = ".Mage::helper('core')->currency($discount_value, true, false);
		}
			Mage::getSingleton('customer/session')->setZeoCustomerDiscount($string_discount);
		
		if($percent == true) {
		    return $customer_discount;
		}
		return $discount_value;
	}
}
	 