<?php
namespace Forix\Csp\Controller\Adminhtml\Report;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Backend\App\Action\Context;
use Forix\Csp\Model\ReportFactory;

/**
 * Save CMS report action.
 */
class Delete extends Action implements HttpPostActionInterface
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
                $model->delete();
                // display success message
                $this->messageManager->addSuccessMessage(__('You deleted the report.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addErrorMessage(__('We can\'t find a report to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}
