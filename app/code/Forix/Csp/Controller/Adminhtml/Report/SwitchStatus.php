<?php
namespace Forix\Csp\Controller\Adminhtml\Report;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Backend\App\Action\Context;
use Forix\Csp\Model\ReportFactory;

/**
 * Save CMS report action.
 */
class SwitchStatus extends Action implements HttpGetActionInterface
{
    /**
     * @var ReportFactory
     */
    private $reportFactory;

    /**
     * @param Context $context
     * @param DataPersistorInterface $dataPersistor
     * @param ReportFactory|null $reportFactory
     * @param BlockRepositoryInterface|null $reportRepository
     */
    public function __construct(
        Context $context,
        ReportFactory $reportFactory = null
    ) {
        $this->reportFactory = $reportFactory ?: \Magento\Framework\App\ObjectManager::getInstance()->get(ReportFactory::class);
        parent::__construct($context);
    }

    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                // init model and delete
                $model = $this->reportFactory->create();
                $model->load($id);
                if(!$model->getId())
                    throw new \Exception(__("This report no longer exists"));
                $newStatus = abs($model->getIsAllowed() - 1);
                $model->setIsAllowed($newStatus)->save();
                // display success message
                $this->messageManager->addSuccessMessage(__('You updated the report.'));
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }
        // go to grid
        return $resultRedirect->setPath('*/*/index');
    }
}
