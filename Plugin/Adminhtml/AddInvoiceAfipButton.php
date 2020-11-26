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
 
namespace Watts25\Afip\Plugin\Adminhtml;

use Magento\Sales\Api\InvoiceRepositoryInterface;
 
class AddInvoiceAfipButton
{
    protected $_helper;
    protected $_invoiceRepository;

    public function __construct
    (
        \Watts25\Afip\Helper\Data $helper,
        InvoiceRepositoryInterface $invoiceRepository
    )
    {
        $this->_helper            = $helper;
        $this->_invoiceRepository = $invoiceRepository;
    }

    /**
     * @param \Magento\Backend\Block\Widget\Button\Toolbar\Interceptor $subject
     * @param \Magento\Framework\View\Element\AbstractBlock $context
     * @param \Magento\Backend\Block\Widget\Button\ButtonList $buttonList
     */
    public function beforePushButtons(
        \Magento\Backend\Block\Widget\Button\Toolbar\Interceptor $subject,
        \Magento\Framework\View\Element\AbstractBlock $context,
        \Magento\Backend\Block\Widget\Button\ButtonList $buttonList
    )
    {
        if ($this->_helper->isEnabled() && $context->getRequest()->getFullActionName() == 'sales_order_invoice_view') {
            $invoice_id = $context->getRequest()->getParam('invoice_id');
            $invoice = $this->_invoiceRepository->get($invoice_id);

            $url = $context->getUrl('watts25_afip/invoice/index', ['invoice_id' => $invoice->getId()]);
            
            $buttonList->add(
                'invoiceafip',
                ['label' => __('Invoice Afip'), 'onclick' => 'setLocation("' . $url . '")', 'class' => 'reset'],
                -1
            );
        }
    }
}