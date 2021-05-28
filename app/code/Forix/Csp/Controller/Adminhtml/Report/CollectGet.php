<?php
/**
 * Created by: Bruce
 * Date: 07/13/20
 * Time: 13:47
 */

namespace Forix\Csp\Controller\Adminhtml\Report;

use Forix\Csp\Helper\ImportData;
use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Exception\NotFoundException;
use Psr\Log\LoggerInterface;

class CollectGet extends Action implements HttpGetActionInterface{

    protected $helper;
    protected $logger;
    /** @var \Magento\Framework\Controller\Result\RedirectFactory */
    protected $resultRedirectFactory;

    public function __construct(
        Action\Context $context,
        LoggerInterface $logger,
        ImportData $helper,
        RedirectFactory $resultRedirectFactory
    ){
        $this->helper = $helper;
        $this->logger = $logger;
        /** @var \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory */
        $this->resultRedirectFactory = $resultRedirectFactory;
        parent::__construct($context);
    }

    public function execute(){
        $resultRedirect = $this->resultRedirectFactory->create();
        try{
            $this->helper->import();
            $this->messageManager->addSuccessMessage(__("New rules imported"));
        }catch(NotFoundException $e){
            $this->messageManager->addErrorMessage($e->getMessage());
        }
        catch(\Exception $e){
            $this->messageManager->addErrorMessage(__("An error has been occurred"));
        }
        return $resultRedirect->setPath('*/*/index');
    }
}
