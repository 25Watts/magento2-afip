<?php
/**
 * Watts25
 *
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Watts25
 * @package     Watts25_Afip
 * @copyright   Copyright (c) Watts25 (https://www.watts25.com.ar/)
 */
 
namespace Watts25\Afip\Controller\Adminhtml\Invoice;

use Magento\Sales\Api\OrderRepositoryInterface;
use Watts25\Afip\Helper\AfipConnect;
use Afip;

class Index extends \Magento\Backend\App\Action
{
    protected $_helper;
    protected $_afipConnect;
    
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Watts25\Afip\Helper\Data $helper,
        AfipConnect $afipConnect
    )
    {
        parent::__construct($context);
        $this->_helper         = $helper;
        $this->_afipConnect    = $afipConnect;
    }

    public function execute()
    {
        try {
            $id = $this->getRequest()->getParam('invoice_id');

            $this->_afipConnect->createInvoice($id);

            $resultRedirect = $this->resultRedirectFactory->create();
 
            return $resultRedirect->setPath('sales/invoice/view', [
                'invoice_id' => $id
            ]);
        } catch (\Exception $e) {
            die(var_dump($e->getMessage()));
        }
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Watts25_FacturarElectronica::watts25_afip');
    }
}
