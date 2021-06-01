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

    /**
     * @param array $data Array data contain hosts URL
     */
    public function mergeHost(array $data){
        $w = [];
        foreach($data as $host){
            if(strpos($host, "*") !== false || strpos($host, ".") === false){
                $w[$host]["csp_url"] = $host;
                continue;
            }
            $_host             = explode(".", $host);
            $l                 = count($_host);
            $key               = $_host[$l-2] . "_" . $_host[$l-1];
            $w[$key]['host'][] = $host;
            if(count($w[$key]['host']) > 1){
                $useUrl             = "*." . $_host[$l-2] . "." . $_host[$l-1];
                $w[$key]["csp_url"] = $useUrl;
            } else{
                $w[$key]["csp_url"] = $host;
            }
        }
        $cspUrl = array_unique(array_column($w,'csp_url'));
        return $cspUrl;
    }
}
