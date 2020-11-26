<?php
namespace Watts25\Afip\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;


class Data extends AbstractHelper
{
    /**
     * @var \Magento\Framework\App\Http\Context
     */
    private $_httpContext;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Http\Context $httpContext
    ) {
        parent::__construct($context);
        $this->_httpContext = $httpContext;
    }
    
    /*
     * @return bool
     */
    public function isEnabled($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        return $this->scopeConfig->isSetFlag(
            'afip/general/is_enabled',
            $scope
        );
    }

    /*
     * @return bool
     */
    public function isProductionMode($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        return $this->scopeConfig->isSetFlag(
            'afip/general/is_production',
            $scope
        );
    }
    
    /*
     * @return string
     */
    public function getCuit($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        return $this->scopeConfig->getValue(
            'afip/general/cuit',
            $scope
        );
    }

    public function getPtoVta($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        return $this->scopeConfig->getValue(
            'afip/general/ptoVta',
            $scope
        );
    }

    public function isObserver($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        return $this->scopeConfig->isSetFlag(
            'afip/general/is_observer',
            $scope
        );
    }
}
