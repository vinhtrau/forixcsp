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

class Collect extends Action{

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
            if(!$this->_formKeyValidator->validate($this->getRequest())){
                $data = ['success' => 0, 'message' => 'Invalid Form key'];
                return $this->resultJsonFactory->setData($data);
            }
            $file = null;
            if($this->getRequest()->getParam('row', false)){
                $file[] = $this->getRequest()->getParam('row',null);
            }
            $this->helper->import($file);
            $data = ['success' => 1];
        }catch(NotFoundException $e){
            $data = ['success' => 0, 'no_file' => 1, 'message' => $e->getMessage()];
        }
        catch(\Exception $e){
            $this->logger->error($e->getMessage());
            $data = ['success' => 0, 'message' => 'An error has been occurred'];
        }
        return $this->resultJsonFactory->setData($data);
    }
}
