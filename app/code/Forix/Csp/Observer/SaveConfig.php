<?php
/**
 * Created by: Bruce
 * Date: 07/13/20
 * Time: 12:27
 */

namespace Forix\Csp\Observer;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class SaveConfig implements ObserverInterface{

    const CONFIG_URI_PATH = "csp/mode/storefront/report_uri";
    const CONFIG_URI_ADMIN_PATH = "csp/mode/admin/report_uri";
    /**
     * @var \Magento\Framework\App\Config\Storage\WriterInterface
     */
    private $configWriter;

    /**
     * @var \Magento\Framework\App\Config\ReinitableConfigInterface
     */
    private $reinitableConfig;

    protected $scopeConfig;

    public function __construct(
        \Magento\Framework\App\Config\ReinitableConfigInterface $reinitableConfig,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
        ScopeConfigInterface $config
    ){
        $this->reinitableConfig = $reinitableConfig;
        $this->scopeConfig      = $config;
        $this->configWriter     = $configWriter;
    }

    public function execute(Observer $observer){
        if($this->scopeConfig->getValue("forix_csp/general/enabled")){
            $requestUri = $this->scopeConfig->getValue("forix_csp/general/report_uri");
            $requestUriAdmin = $this->scopeConfig->getValue("forix_csp/general/report_uri_admin");
            $this->saveConfig(self::CONFIG_URI_PATH, $requestUri);
            $this->saveConfig(self::CONFIG_URI_ADMIN_PATH, $requestUriAdmin);
        } else{
            $this->configWriter->delete(self::CONFIG_URI_PATH);
            $this->configWriter->delete(self::CONFIG_URI_ADMIN_PATH);
            $this->reinitableConfig->reinit();
        }
    }
    /**
     * @param string $path
     * @param string $value
     * @param int $store
     *
     * @return $this
     */
    private function saveConfig($path, $value, $store = null)
    {
        if ($store) {
            $this->configWriter->save($path, $value, \Magento\Store\Model\ScopeInterface::SCOPE_STORES, $store);
        } else {
            $this->configWriter->save($path, $value);
        }
        $this->reinitableConfig->reinit();

        return $this;
    }
}
