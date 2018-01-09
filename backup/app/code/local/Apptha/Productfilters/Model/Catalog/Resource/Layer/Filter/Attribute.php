<?php
/**
 * Apptha
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.apptha.com/LICENSE.txt
 *
 * ==============================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * ==============================================================
 * This package designed for Magento COMMUNITY edition
 * Apptha does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * Apptha does not provide extension support in case of
 * incorrect edition usage.
 * ==============================================================
 *
 * @category    Apptha
 * @package     Apptha_Marketplace
 * @version     1.9.2
 * @author      Apptha Team <developers@contus.in>
 * @copyright   Copyright (c) 2016 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 *
 */
class Apptha_Productfilters_Model_Catalog_Resource_Layer_Filter_Attribute extends Mage_Catalog_Model_Resource_Layer_Filter_Attribute{

    /**
     * Apply attribute filter to product collection
     *
     * @param Mage_Catalog_Model_Layer_Filter_Attribute $filter
     * @param int $value
     * @return Mage_Catalog_Model_Resource_Layer_Filter_Attribute
     */
    public function applyFilterToCollection($filter, $value) {
        if (! Mage::helper ( 'productfilters' )->isEnabled ()) {
            return parent::applyFilterToCollection ( $filter, $value );
        }

        $collection = $filter->getLayer ()->getProductCollection ();
        $attribute = $filter->getAttributeModel ();
        $connection = $this->_getReadAdapter ();
        $tableAlias = $attribute->getAttributeCode () . '_idx_' . $value;
        $conditions = array (
                "{$tableAlias}.entity_id = e.entity_id",
                $connection->quoteInto ( "{$tableAlias}.attribute_id = ?", $attribute->getAttributeId () ),
                $connection->quoteInto ( "{$tableAlias}.store_id = ?", $collection->getStoreId () )
        );

        $options=array();
        if (! is_array ( $value )) {
            foreach ( $options as $option ) {
                if ($option ['label'] == $value) {
                    $value = $option ['value'];
                }
            }
            $conditions [] = $connection->quoteInto ( "{$tableAlias}.value = ?", $value );
        } else {
            $conditions [] = "{$tableAlias}.value in ( ";
            foreach ( $value as $v ) {
                $conditions [count ( $conditions ) - 1] .= $connection->quoteInto ( "?", $v ) . ' ,';
            }
            $conditions [count ( $conditions ) - 1] = rtrim ( $conditions [count ( $conditions ) - 1], ',' );
            $conditions [count ( $conditions ) - 1] .= ')';
        }

        $collection->getSelect ()->join ( array (
                $tableAlias => $this->getMainTable ()
        ), implode ( ' AND ', $conditions ), array () );
        $collection->getSelect ()->distinct ();

        return $this;
    }

    /**
     * Retrieve array with products counts per attribute option
     *
     * @param Mage_Catalog_Model_Layer_Filter_Attribute $filter
     * @return array
     */
    public function getCount($filter) {
        if (! Mage::helper ( 'productfilters' )->isEnabled ()) {
            return parent::getCount ( $filter );
        }

        $select = clone $filter->getLayer ()->getProductCollection ()->getSelect ();
        $select->reset ( Zend_Db_Select::COLUMNS );
        $select->reset ( Zend_Db_Select::ORDER );
        $select->reset ( Zend_Db_Select::LIMIT_COUNT );
        $select->reset ( Zend_Db_Select::LIMIT_OFFSET );
        $connection = $this->_getReadAdapter ();
        $attribute = $filter->getAttributeModel ();
        $tableAlias = sprintf ( '%s_idx', $attribute->getAttributeCode () );
        $conditions = array (
                "{$tableAlias}.entity_id = e.entity_id",
                $connection->quoteInto ( "{$tableAlias}.attribute_id = ?", $attribute->getAttributeId () ),
                $connection->quoteInto ( "{$tableAlias}.store_id = ?", $filter->getStoreId () )
        );
        $parts = $select->getPart ( Zend_Db_Select::FROM );
        $from = array ();
        foreach ( $parts as $key => $part ) {
            if (stripos ( $key, $tableAlias ) === false) {
                $from [$key] = $part;
            }
        }
        $select->setPart ( Zend_Db_Select::FROM, $from );
        $select->join ( array (
                $tableAlias => $this->getMainTable ()
        ), join ( ' AND ', $conditions ), array (
                'value',
                'count' => new Zend_Db_Expr ( "COUNT({$tableAlias}.entity_id)" )
        ) )->group ( "{$tableAlias}.value" );
        return $connection->fetchPairs ( $select );
    }
}