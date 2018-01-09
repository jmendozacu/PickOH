<?php
$installer = $this;
$installer->startSetup();


$installer->addAttribute("catalog_category", "homepagecategory",  array(
    "type"     => "int",
    "backend"  => "",
    "frontend" => "",
    "label"    => "Homepage Category",
    "input"    => "select",
    "class"    => "",
    "source"   => "categoryattrbute/eav_entity_attribute_source_categoryoptions14972801130",
    "global"   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    "visible"  => true,
    "required" => false,
    "user_defined"  => false,
    "default" => "No",
    "searchable" => false,
    "filterable" => false,
    "comparable" => false,
	
    "visible_on_front"  => false,
    "unique"     => false,
    "note"       => ""

	));
$installer->endSetup();
	 