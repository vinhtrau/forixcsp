<?php
/**
 * Created by: Bruce
 * Date: 07/13/20
 * Time: 10:18
 */

namespace Forix\Csp\Controller\Index;

use Magento\Csp\Api\CspAwareActionInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Controller\Result\JsonFactory;

class Report extends Action implements CspAwareActionInterface{

    protected $resultJsonFactory;
    public function __construct(
        Context $context, ScopeConfigInterface $scopeConfig = null
    ){
        $this->scopeConfig = $scopeConfig ?: ObjectManager::getInstance()->get(ScopeConfigInterface::class);
        /** Magento\Framework\Controller\Result\JsonFactory*/
        $this->resultJsonFactory = ObjectManager::getInstance()->get(JsonFactory::class);
        parent::__construct($context);
    }

    public function execute()
    {
        //test only, not working
        $result = $this->resultJsonFactory->create();

        $result->setData(['output' => "asdasd"]);
        return $result;
    }

    public function modifyCsp(array $appliedPolicies): array{
        return $appliedPolicies;
        // TODO: Implement modifyCsp() method.
    }
}
