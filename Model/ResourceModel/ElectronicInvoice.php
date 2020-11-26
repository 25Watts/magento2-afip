<?php
namespace Watts25\Afip\Model\ResourceModel;


class ElectronicInvoice extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
	public function __construct(
		\Magento\Framework\Model\ResourceModel\Db\Context $context
	)
	{
		parent::__construct($context);
	}
	
	protected function _construct()
	{
		$this->_init('watts25_afip', 'entity_id');
	}
}