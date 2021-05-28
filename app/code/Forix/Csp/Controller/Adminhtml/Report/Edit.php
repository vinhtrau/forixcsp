<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Forix\Csp\Controller\Adminhtml\Report;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class Edit extends Action implements HttpGetActionInterface{
    protected $resultPageFactory;
    protected $resultLayoutFactory;
    protected $resultForwardFactory;
    protected $reportFactory;

    public function __construct(
        Context $context,
        \Forix\Csp\Model\ReportFactory $reportFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
    ){
        $this->reportFactory        = $reportFactory;
        $this->resultPageFactory    = $resultPageFactory;
        $this->resultLayoutFactory  = $resultLayoutFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        parent::__construct($context);
    }

    public function execute(){
        $id = $this->getRequest()->getParam('id');
        $model = $this->reportFactory->create();
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This rule no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }

        $data = $this->_getSession()->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Forix_Csp::reports')
                   ->addBreadcrumb(__('CSP'), __('CSP'))
                   ->addBreadcrumb(__('Reports'), __('Reports'))
            ->addBreadcrumb(
                $id ? __('Edit Report') : __('New Report'),
                $id ? __('Edit Report') : __('New Report')
            );
        $resultPage->getConfig()->getTitle()->prepend($model->getId() ? __("Edit report %1", $model->getHost()) : __('New Report'));
        return $resultPage;
    }
}
