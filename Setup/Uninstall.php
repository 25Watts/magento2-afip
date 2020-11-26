<?php

/**
 * @Author: beto
 * @Date:   2019-07-02 13:29:12
 * @Last Modified by:   beto
 * @Last Modified time: 2019-07-03 16:24:21
 */

namespace Watts25\Quotation\Setup;

use Magento\Framework\Setup\UninstallInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class Uninstall implements UninstallInterface
{
	public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
	{
		$installer = $setup;
		$installer->startSetup();

		$installer->getConnection()->dropTable($installer->getTable('watts25_afip'));

		$installer->endSetup();
	}
}