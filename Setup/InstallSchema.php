<?php

namespace Watts25\Afip\Setup;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{
	public function install(\Magento\Framework\Setup\SchemaSetupInterface $setup, \Magento\Framework\Setup\ModuleContextInterface $context)
	{
		$installer = $setup;
		$installer->startSetup();
		if (!$installer->tableExists('watts25_afip')) {
			$table = $installer->getConnection()->newTable(
				$installer->getTable('watts25_afip')
			)
				->addColumn(
					'entity_id',
					\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					null,
					[
						'identity' => true,
						'nullable' => false,
						'primary'  => true,
						'unsigned' => true,
					],
					'entity ID'
				)
				->addColumn(
					'invoice_id',
					\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					10,
					[
						'nullable' => false, 
						'unsigned' => true
					],
					'relation sales_invoice'
				)
				->addColumn(
					'admin_user_id',
					\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					10,
					[
						'nullable' => true, 
						'unsigned' => true
					],
					'relation admin_user_id'
				)
				->addColumn(
					'cae',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					20,
					[],
					'CAE'
				)
				->addColumn(
					'cae_due_date',
					\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
					20,
					[],
					'CAE Due Date'
				)
				->addColumn(
					'cae_voucher_number',
					\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					10,
					[
						'nullable' => true, 
						'unsigned' => true
					],
					'CAE voucher number'
				)
				->addColumn(
					'status',
					\Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
					1,
					[],
					'CAE return status'
				)
				->addColumn(
					'message',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					null,
					[],
					'CAE return message'
				)
				->addColumn(
					'created_at',
					\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
					null,
					[
						'nullable' => false, 
						'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT
					],
					'Created At'
				)->addColumn(
					'updated_at',
					\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
					null,
					[
						'nullable' => false, 
						'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE
					],
					'Updated At')
				->addForeignKey(
				    $installer->getFkName('watts25_afip', 'invoice_id', 'sales_invoice', 'entity_id'),
				    'invoice_id',
				    $installer->getTable('sales_invoice'), 
				    'entity_id',
				    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
				)
				->addForeignKey(
				    $installer->getFkName('watts25_afip', 'admin_user_id', 'admin_user', 'user_id'),
				    'admin_user_id',
				    $installer->getTable('admin_user'), 
				    'user_id',
				    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
				)
				->setComment('Electronic Billing');
			$installer->getConnection()->createTable($table);
		}
		
		$installer->endSetup();
	}
}