<?php
namespace Forix\Csp\Controller\Adminhtml\Report;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Backend\App\Action\Context;
use Forix\Csp\Api\ReportRepositoryInterface;
use Forix\Csp\Model\ReportFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Save CMS block action.
 */
class Save extends Action implements HttpPostActionInterface
{
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var ReportFactory
     */
    private $reportFactory;

    /**
     * @param Context $context
     * @param DataPersistorInterface $dataPersistor
     * @param ReportFactory|null $reportFactory
     * @param BlockRepositoryInterface|null $blockRepository
     */
    public function __construct(
        Context $context,
        DataPersistorInterface $dataPersistor,
        ReportFactory $reportFactory = null
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->reportFactory = $reportFactory
            ?: \Magento\Framework\App\ObjectManager::getInstance()->get(ReportFactory::class);
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            if (empty($data['id'])) {
                $data['id'] = null;
            }

            /** @var \Forix\Csp\Model\Report $model */
            $model = $this->reportFactory->create();

            $id = $this->getRequest()->getParam('id');
            $model = $this->reportFactory->create();
            if($id){
                $model->load($id);
                if(!$model->getId()){
                    $this->messageManager->addErrorMessage(__('This report no longer exists.'));
                    return $resultRedirect->setPath('*/*/');
                }
            }

            $model->setData($data);

            try {
                $model->save();
                $this->messageManager->addSuccessMessage(__('You saved the report.'));
                $this->dataPersistor->clear('csp_report');
                return $this->processReportReturn($model, $data, $resultRedirect);
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the report.'));
            }

            $this->dataPersistor->set('csp_report', $data);
            return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Process and set the report return
     *
     * @param \Forix\Csp\Model\Report $model
     * @param array $data
     * @param \Magento\Framework\Controller\ResultInterface $resultRedirect
     * @return \Magento\Framework\Controller\ResultInterface
     */
    private function processReportReturn($model, $data, $resultRedirect)
    {
        $redirect = $data['back'] ?? 'close';

        if ($redirect ==='continue') {
            $resultRedirect->setPath('*/*/edit', ['id' => $model->getId()]);
        } else if ($redirect === 'close') {
            $resultRedirect->setPath('*/*/');
        } else if ($redirect === 'duplicate') {
            $duplicateModel = $this->reportFactory->create(['data' => $data]);
            $duplicateModel->setId(null);
            $duplicateModel->setHost($data['host'] . '-' . uniqid());
            $duplicateModel->setDirective($data['directive'] . '-' . uniqid());
            $duplicateModel->setIsAllwed(0);
            $duplicateModel->save();
            $id = $duplicateModel->getId();
            $this->messageManager->addSuccessMessage(__('You duplicated the report.'));
            $this->dataPersistor->set('csp_report', $data);
            $resultRedirect->setPath('*/*/edit', ['id' => $id]);
        }
        return $resultRedirect;
    }
}
