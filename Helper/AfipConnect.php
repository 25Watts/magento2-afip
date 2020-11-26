<?php
namespace Watts25\Afip\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Watts25\Afip\Helper\Data;
use Afip;

class AfipConnect extends AbstractHelper
{
    protected $_helper;
    protected $_messageManager;
    private $_objectManager;

    /**
     * @var InvoiceRepositoryInterface
     */
    protected $_invoiceRepository;

    protected $_logger;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        InvoiceRepositoryInterface $invoiceRepository,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\ObjectManagerInterface $objectmanager,
        Data $helper
    ) {
        parent::__construct($context);
        
        $this->_invoiceRepository = $invoiceRepository;
        $this->_messageManager    = $messageManager;
        $this->_request           = $request;
        $this->_objectManager     = $objectmanager;
        $this->_helper            = $helper;
    }

    public function createInvoice($invoice_id)
    {  	
        $writer = new \Zend\Log\Writer\Stream(BP.'/var/log/watts25_facturalelectronica.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $items = array();
        $logCollection = $this->_objectManager->create('Watts25\Afip\Model\ResourceModel\ElectronicInvoice\Collection');
        $logCollection
            ->addFieldToFilter('invoice_id',$invoice_id)
            ->addFieldToFilter('status','1')
            ->setOrder('created_at', 'DESC');

        if ($logCollection->getSize() >= 1) {
            $log = $logCollection->getFirstItem();
            $this->_messageManager->addWarningMessage(__('Afip Invoice CAE %1 already created at %2', $log->getData('cae'), $log->getData('created_at')));

            return false;
        }

        try {
            $invoice = $this->_invoiceRepository->get($invoice_id);

            // Fetch total order information
            $invoiceTotalAmount     = $invoice->getGrandTotal();
            $invoiceTotalWithoutIva = $invoiceTotalAmount / 1.21;
            $ivaAmount              = $invoiceTotalAmount - $invoiceTotalWithoutIva;
            // $invoiceItems           = $invoice->getAllItems();

            // foreach ($invoiceItems as $item) {
            //     $items[] = array(
            //         'Id'      =>  99, // Id del tipo de tributo (ver tipos disponibles) 
            //         'Desc'    => $item->getName(), // (Opcional) Descripcion
            //         'BaseImp' => number_format($item->getPrice(), 2, '.', ''), // Base imponible para el tributo
            //         'Alic'    => 21, // Alícuota
            //         'Importe' => number_format($item->getPrice() / 1.21, 2, '.', '')// Importe del tributo
            //     );
            // }

            // get Buyer Information DNI
            $customerDni = $invoice->getCheckoutBuyerEmail();
        } catch (NoSuchEntityException $e) {
            $this->_messageManager->addErrorMessage(__('This invoice no longer exists.'));

            return false;
        } catch (InputException $e) {
            $this->_messageManager->addErrorMessage(__('This invoice no longer exists.'));

            return false;
        }

        $getCUIT           = $this->_helper->getCuit();
        $getProductionMode = $this->_helper->isProductionMode();
        $getPtoVta         = $this->_helper->getPtoVta();

        $data = array(
            'CantReg'    => 1,  // Cantidad de comprobantes a registrar
            'PtoVta'     => $getPtoVta,  // Punto de venta
            'CbteTipo'   => 6,  // Tipo de comprobante (ver tipos disponibles) 
            'Concepto'   => 1,  // Concepto del Comprobante: (1)Productos, (2)Servicios, (3)Productos y Servicios
            'DocTipo'    => 99, // Tipo de documento del comprador (99 consumidor final, ver tipos disponibles)
            'DocNro'     => 0,  // Número de documento del comprador (0 consumidor final)
            'CbteDesde'  => 1,  // Número de comprobante o numero del primer comprobante en caso de ser mas de uno
            'CbteHasta'  => 1,  // Número de comprobante o numero del último comprobante en caso de ser mas de uno
            'CbteFch'    => intval(date('Ymd')), // (Opcional) Fecha del comprobante (yyyymmdd) o fecha actual si es nulo
            'ImpTotal'   => number_format($invoiceTotalAmount, 2, '.', ''), // Importe total del comprobante
            'ImpTotConc' => 0,   // Importe neto no gravado
            'ImpNeto'    => number_format($invoiceTotalWithoutIva, 2, '.', ''), // Importe neto gravado
            'ImpOpEx'    => 0,   // Importe exento de IVA
            'ImpIVA'     => number_format($ivaAmount, 2, '.', ''),  //Importe total de IVA
            'ImpTrib'    => 0,   //Importe total de tributos
            'MonId'      => 'PES', //Tipo de moneda usada en el comprobante (ver tipos disponibles)('PES' para pesos argentinos) 
            'MonCotiz'   => 1,     // Cotización de la moneda usada (1 para pesos argentinos)  
            'Iva'        => array( // (Opcional) Alícuotas asociadas al comprobante
                array(
                    'Id'      => 5, // Id del tipo de IVA (5 para 21%)(ver tipos disponibles) 
                    'BaseImp' => number_format($invoiceTotalWithoutIva, 2, '.', ''), // Base imponible
                    'Importe' => number_format($ivaAmount, 2, '.', '') // Importe 
                )
            )
        );

        // Check if production mode is set to ON
        if ($getProductionMode == 1 ) {
            $isProductionMode = TRUE;
        } else {
            $isProductionMode = FALSE;
        }

        $afip = new \Afip(array(
            'CUIT'       => $getCUIT,
            'production' => $isProductionMode
        ));
        $invoiceData = [
            'invoice_id' => $invoice->getId(),
            'status'   => 1,
        ];
        
        try {
            $result = $afip->ElectronicBilling->CreateNextVoucher($data);

            // set CAE data
            $invoiceData['cae']                = $result['CAE'];
            $invoiceData['cae_due_date']       = strtotime($result['CAEFchVto']);
            $invoiceData['cae_voucher_number'] = $result['voucher_number'];

            $logger->info($data);
            $logger->info($result);
            $logger->info('----------------------------');
            $this->_messageManager->addSuccessMessage(__('Invoiced Created AFIP CAE %1', $result['CAE']));

            // save response and order relation
            $model = $this->_objectManager->create('Watts25\Afip\Model\ElectronicInvoice');

            // get current user ID
            $userAdmin = $this->_objectManager->get('Magento\Backend\Model\Auth\Session')->getUser();
            if ($userAdmin) {
                $invoiceData['admin_user_id'] = $userAdmin->getId();
            }
            
            // save data
            $model
                ->addData($invoiceData)
                ->save();

            return $result;
        } catch (\Exception $e) {
            $invoiceData['status']  = FALSE;
            $invoiceData['message'] = $e->getMessage();

            $this->_messageManager->addErrorMessage(__($e->getMessage()));
        }
    }
}
