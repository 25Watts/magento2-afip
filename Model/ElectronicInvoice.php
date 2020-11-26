<?php
namespace Watts25\Afip\Model;

class ElectronicInvoice extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
	const CACHE_TAG = 'watts25_afip_electronicinvoice';

	protected $_cacheTag = 'watts25_afip_electronicinvoice';

	protected $_eventPrefix = 'watts25_afip_electronicinvoice';

	protected function _construct()
	{
		$this->_init('Watts25\Afip\Model\ResourceModel\ElectronicInvoice');
	}

	public function getIdentities()
	{
		return [self::CACHE_TAG . '_' . $this->getId()];
	}

	public function getDefaultValues()
	{
		$values = [];

		return $values;
	}
}