<?php
/**
 * Created by: Bruce
 * Date: 07/13/20
 * Time: 13:47
 */

namespace Forix\Csp\Controller\Adminhtml\Report;

use Forix\Csp\Helper\ImportData;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NotFoundException;
use Psr\Log\LoggerInterface;

class ReportedList extends Action{

    protected $helper;
    protected $logger;
    /** @var \Magento\Framework\Controller\Result\Json */
    protected $resultJsonFactory;

    public function __construct(
        Action\Context $context,
        LoggerInterface $logger,
        ImportData $helper
    ){
        $this->helper = $helper;
        $this->logger = $logger;
        /** @var \Magento\Framework\Controller\Result\Json resultJsonFactory */
        $this->resultJsonFactory = $context->getResultFactory()->create('json');
        parent::__construct($context);
    }

    public function execute(){
        try{
            $data = $this->helper->getReportedList();
            $list = [];
            if(isset($data['frontend']) && count($data['frontend']))
                $list = array_merge($list,$data['frontend']);
            if(isset($data['admin']) && count($data['admin']))
                $list = array_merge($list,$data['admin']);
            $data = ['data' => $list];
        }catch(NotFoundException $e){
            $this->messageManager->addWarningMessage($e->getMessage());
            $data = ['success' => 0, 'message' => $e->getMessage()];
        }
        catch(\Exception $e){
            $this->messageManager->addError("An error has been occurred");
            $this->logger->error($e->getMessage());
            $data = ['success' => 0, 'message' => 'An error has been occurred'];
        }
        return $this->resultJsonFactory->setData($data);
    }
}
