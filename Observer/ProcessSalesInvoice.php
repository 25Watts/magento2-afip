<?php

/**
 * @Author: beto
 * @Date:   2019-07-02 13:27:05
 * @Last Modified by:   merge con develop
 * @Last Modified time: 2019-11-19 16:09:24
 */
namespace Watts25\Afip\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Watts25\Afip\Helper\Data;
use Watts25\Afip\Helper\AfipConnect;
use Afip;

class ProcessSalesInvoice implements ObserverInterface
{
    protected $_helper;
    protected $_afipConnect;

    private $_objectManager;
    
    public function __construct(
        Data $helper,
        AfipConnect $afipConnect
    )
    {
        $this->_helper         = $helper;
        $this->_afipConnect    = $afipConnect;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->_helper->isObserver() === false )
            return;

        try {
            $id = $observer->getEvent()->getInvoice()->getId();

            $this->_afipConnect->createInvoice($id);
        } catch (\Exception $e) {
            
        }
    }
}