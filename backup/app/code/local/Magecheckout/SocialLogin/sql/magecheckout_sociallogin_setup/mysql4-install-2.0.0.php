<?php
$installer = $this;

$installer->startSetup();

$installer->getConnection()->dropTable($installer->getTable('sociallogin/author'));
$table = $installer->getConnection()
    ->newTable($installer->getTable('sociallogin/author'))
    ->addColumn(
        'entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary'  => true,
    ),
        'Entity Api ID'
    )
    ->addColumn(
        'token_id',
        Varien_Db_Ddl_Table::TYPE_VARCHAR, 255,
        array(
            'nullable' => false,
            'unsigned' => true,
        ),
        'Token Id'
    )
    ->addColumn(
        'customer_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER, 10,
        array(
            'nullable' => false,
            'unsigned' => true,
        ),
        'Customer ID'
    )
    ->addColumn(
        'is_send_password_email',
        Varien_Db_Ddl_Table::TYPE_SMALLINT, null,
        array(
            'nullable' => false,
            'default'  => 0
        ),
        'Send Password Email'
    )
    ->addColumn(
        'type',
        Varien_Db_Ddl_Table::TYPE_TEXT, 255,
        array(
            'nullable' => false,
        ),
        'Social Type'
    )
    ->addColumn(
        'status',
        Varien_Db_Ddl_Table::TYPE_SMALLINT, null,
        array(),
        'Enabled'
    )
    ->addForeignKey($installer->getFkName('sociallogin/author', 'customer_id', 'customer/entity', 'entity_id'),
        'customer_id', $installer->getTable('customer/entity'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Social Api Table');
$installer->getConnection()->createTable($table);
$installer->endSetup();
