<?php
/**
 * Created by: Bruce
 * Date: 26/05/2021
 * Time: 17:23
 */

namespace Forix\Csp\Controller\Adminhtml\Report;
class Index extends \Magento\Backend\App\Action{

    protected $resultPageFactory = false;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ){
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute(){
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend((__('Manage rules')));

        return $resultPage;
    }

    protected function _isAllowed(){
        return true;
    }
}
