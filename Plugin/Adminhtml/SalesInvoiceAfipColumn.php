<?php
namespace Watts25\Afip\Plugin\Adminhtml;

use Magento\Framework\Message\ManagerInterface as MessageManager;
use Magento\Sales\Model\ResourceModel\Order\Invoice\Grid\Collection as SalesOrderInvoiceGridCollection;

class SalesInvoiceAfipColumn
{
    private $messageManager;
    private $collection;

    public function __construct(
        MessageManager $messageManager,
        SalesOrderInvoiceGridCollection $collection
    ) {
        $this->messageManager = $messageManager;
        $this->collection = $collection;
    }

    public function aroundGetReport(
        \Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory $subject,
        \Closure $proceed,
        $requestName
    ) {
        $result = $proceed($requestName);
        if ($requestName == 'sales_order_invoice_grid_data_source') {
            if ($result instanceof $this->collection
            ) {
                $select = $this->collection->getSelect();
                $select->joinLeft(
                    ["watts25_afip" => $this->collection->getTable("watts25_afip")],
                    'main_table.entity_id = watts25_afip.invoice_id',
                    array('cae')
                );
                return $this->collection;
            }
        }
        return $result;
    }
}