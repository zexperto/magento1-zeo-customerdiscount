<?php
$installer = $this;
$installer->startSetup();


$installer->addAttribute("customer", "zeo_customer_discount",  array(
    "type"     => "varchar",
    "backend"  => "",
    "label"    => "Discount (%)",
    "input"    => "text",
    "source"   => "",
    "visible"  => true,
    "required" => false,
    "default" => "",
    "frontend" => "",
    "unique"     => false,
    "note"       => ""

	));

        $attribute   = Mage::getSingleton("eav/config")->getAttribute("customer", "zeo_customer_discount");

        
$used_in_forms=array();

$used_in_forms[]="adminhtml_customer";
        $attribute->setData("used_in_forms", $used_in_forms)
		->setData("is_used_for_customer_segment", true)
		->setData("is_system", 0)
		->setData("is_user_defined", 1)
		->setData("is_visible", 1)
		->setData("sort_order", 100)
		;
        $attribute->save();
	

$installer->addAttribute("order", "zeo_customer_discount", array("type"=>"varchar"));
$installer->getConnection()->addColumn($installer->getTable('sales_flat_order'), "zeo_customer_discount", "VARCHAR(50) NOT NULL DEFAULT ''");

	
$installer->endSetup();
	 