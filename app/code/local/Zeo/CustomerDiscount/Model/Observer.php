<?php
class Zeo_CustomerDiscount_Model_Observer
{

  public function saveOrderAfter($observer)
  {
    $string_discount = Mage::getSingleton('customer/session')->getZeoCustomerDiscount();
    if ($string_discount != "") {
      $order = $observer->getEvent()->getOrder();
      $order->setData('zeo_customer_discount', $string_discount);
      $order->save();
    }
    exit;

  }
  public function setDiscount(Varien_Event_Observer $observer)
  {
    $quote          = $observer->getEvent()->getQuote();
    $quoteid        = $quote->getId();
    $discountAmount = Mage::helper('customerdiscount')->getCustomerDiscount();
    $discountPercent = Mage::helper('customerdiscount')->getCustomerDiscount(true);
    $apply_tax_discount = Mage::helper('customerdiscount')->getApplyDiscountonTax();
    
    $tax_value = 0;
    if ($quoteid) {
      if ($discountAmount > 0) {
        $total       = $quote->getBaseSubtotal();
        $quote->setSubtotal(0);
        $quote->setBaseSubtotal(0);

        $quote->setSubtotalWithDiscount(0);
        $quote->setBaseSubtotalWithDiscount(0);

        $quote->setGrandTotal(0);
        $quote->setBaseGrandTotal(0);


        $canAddItems = $quote->isVirtual()? ('billing') : ('shipping');
        foreach ($quote->getAllAddresses() as $address) {

          $address->setSubtotal(0);
          $address->setBaseSubtotal(0);

          $address->setGrandTotal(0);
          $address->setBaseGrandTotal(0);

          $address->collectTotals();

          // Tax
          if($apply_tax_discount == true){
            $tax_amount = $address->getTaxAmount();
              $tax_value = $discountPercent*$tax_amount/100;
              $address->setTaxAmount($tax_amount - $tax_value);
              
              $base_tax_amount = $address->getBaseTaxAmount();
              $base_tax_value = $discountPercent*$base_tax_amount/100;
              $address->setBaseTaxAmount($base_tax_amount - $base_tax_value);
          }
          // End Tax
          
          $quote->setSubtotal((float) $quote->getSubtotal() + $address->getSubtotal());
          $quote->setBaseSubtotal((float) $quote->getBaseSubtotal() + $address->getBaseSubtotal());

          $quote->setSubtotalWithDiscount(
            (float) $quote->getSubtotalWithDiscount() + $address->getSubtotalWithDiscount()
          );
          $quote->setBaseSubtotalWithDiscount(
            (float) $quote->getBaseSubtotalWithDiscount() + $address->getBaseSubtotalWithDiscount()
          );

          $quote->setGrandTotal((float) $quote->getGrandTotal() + $address->getGrandTotal());
          $quote->setBaseGrandTotal((float) $quote->getBaseGrandTotal() + $address->getBaseGrandTotal());

          $quote ->save();

          $quote->setGrandTotal($quote->getBaseSubtotal() - $discountAmount -$tax_value);
          $quote->setBaseGrandTotal($quote->getBaseSubtotal() - $discountAmount - $base_tax_value);
          $quote->setSubtotalWithDiscount($quote->getBaseSubtotal() - $discountAmount -$tax_value);
          $quote->setBaseSubtotalWithDiscount($quote->getBaseSubtotal() - $discountAmount -$base_tax_value);
          $quote->save();


          if ($address->getAddressType() == $canAddItems) {
            //echo $address->setDiscountAmount; exit;
              $address->setSubtotalWithDiscount((float) $address->getSubtotalWithDiscount() - $discountAmount -$tax_value);
              $address->setGrandTotal((float) $address->getGrandTotal() - $discountAmount - $tax_value);
              $address->setBaseSubtotalWithDiscount((float) $address->getBaseSubtotalWithDiscount() - $discountAmount - $base_tax_value);
              $address->setBaseGrandTotal((float) $address->getBaseGrandTotal() - $discountAmount - $base_tax_value);

            if ($address->getDiscountDescription()) {
              $address->setDiscountAmount( - ($address->getDiscountAmount() - $discountAmount));
              $address->setDiscountDescription($address->getDiscountDescription().', Custom Discount');
              $address->setBaseDiscountAmount( - ($address->getBaseDiscountAmount() - $discountAmount));
            }
            else {
              $address->setDiscountAmount( - ($discountAmount));
              $address->setDiscountDescription('Customr Discount');
              $address->setBaseDiscountAmount( - ($discountAmount));
            }

            $address->save();
          }//end: if
        } //end: foreach
        //echo $quote->getGrandTotal();

        foreach ($quote->getAllItems() as $item) {
          //We apply discount amount based on the ratio between the GrandTotal and the RowTotal
          $rat=$item->getPriceInclTax()/$total;
          $ratdisc=$discountAmount*$rat;
          $item->setDiscountAmount(($item->getDiscountAmount()+$ratdisc) * $item->getQty());
          $item->setBaseDiscountAmount(($item->getBaseDiscountAmount()+$ratdisc) * $item->getQty())->save();

        }


      }

    }
  }

}
