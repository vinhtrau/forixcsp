<?php
/**
 * Created by: Bruce
 * Date: 28/05/2021
 * Time: 10:59
 */
namespace Forix\Csp\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class Config extends AbstractHelper{

    protected $scopeConfig;
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig
    ){
        parent::__construct($context);
        $this->scopeConfig = $scopeConfig;
    }
    public function isEnableReport(){
        return $this->scopeConfig->getValue('forix_csp/general/enabled');
    }
    public function isEnableRule(){
        return $this->scopeConfig->getValue('forix_csp/general/apply_rules');
    }
    public function getReportUri(){
        return $this->scopeConfig->getValue('forix_csp/general/report_uri');
    }
    public function getAdminReportUri(){
        return $this->scopeConfig->getValue('forix_csp/general/report_uri_admin');
    }
}
