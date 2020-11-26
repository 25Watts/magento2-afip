<?php
namespace Watts25\Afip\Model\ResourceModel\ElectronicInvoice;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
	protected $_idFieldName = 'entity_id';
	protected $_eventPrefix = 'watts25_afip_electronicinvoice_collection';
	protected $_eventObject = 'electronicinvoice_collection';

	/**
	 * Define resource model
	 *
	 * @return void
	 */
	protected function _construct()
	{
		$this->_init('Watts25\Afip\Model\ElectronicInvoice', 'Watts25\Afip\Model\ResourceModel\ElectronicInvoice');
	}

}