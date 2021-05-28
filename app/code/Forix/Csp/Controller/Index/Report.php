<?php
/**
 * Created by: Bruce
 * Date: 07/13/20
 * Time: 10:18
 */

namespace Forix\Csp\Controller\Index;

use Magento\Csp\Model\Policy\FetchPolicy;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Filesystem\DirectoryList;

class Report extends Action implements \Magento\Framework\App\CsrfAwareActionInterface{

    protected $httpResponse;
    /** @var DirectoryList */
    protected $_dir;
    public function __construct(
        Context $context,
        \Magento\Framework\App\Response\HttpFactory $factHttpResponse,
        DirectoryList $dir,
        ScopeConfigInterface $scopeConfig = null
    ){
        $this->scopeConfig = $scopeConfig ?: ObjectManager::getInstance()->get(ScopeConfigInterface::class);
        /** Magento\Framework\Controller\Result\JsonFactory*/
        $this->httpResponse = $factHttpResponse;
        $this->_dir = $dir;
        parent::__construct($context);
    }

    public function execute()
    {
        /** @var \Magento\Framework\App\Response\Http $result */
        $jsonData = file_get_contents("php://input");
        if(substr($jsonData, 0, 14) === '{"csp-report":'){
            $jsonData      = json_decode($jsonData, true);
            $cspReportData = $jsonData['csp-report'];
            $blockUrl = parse_url($cspReportData['blocked-uri']);
            if($cspReportData['blocked-uri'] == "data"){
                $host = "data:";
            } else{
                $host = $blockUrl['host'];
            }
            if(empty($host)){
                return;
            }
            $directive = $cspReportData['violated-directive'];
            $directive = str_replace("-elem","",$directive);
            $date       = date("Y-m-d H:i:s");
            $csvDirPath = $this->_dir->getPath('var') . "/csp_collector/frontend";
            if(!is_dir($csvDirPath)){
                mkdir($csvDirPath, 0777, true);
            }
            if(is_dir($csvDirPath) && is_writable($csvDirPath)){
                $csvName = "csp_" . date("Y_m_d") . ".csv";
                $csvFile = fopen($csvDirPath . "/" . $csvName, 'a+');
                fputcsv($csvFile, [$host, $directive, $date]);
            } else{

            }
        }

        $result = $this->httpResponse->create();
        $result->setHttpResponseCode(204);
        $result->setPublicHeaders(0);
        return $result;
    }
    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @return InvalidRequestException|null
     */
    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @return bool|null
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }
}
