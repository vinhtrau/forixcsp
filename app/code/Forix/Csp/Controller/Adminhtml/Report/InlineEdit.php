<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Forix\Csp\Controller\Adminhtml\Report;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Forix\Csp\Model\ReportFactory;

class InlineEdit extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Forix_Csp::report';

    protected $reportFactory;
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $jsonFactory;

    /**
     * @param Context $context
     * @param JsonFactory $jsonFactory
     */
    public function __construct(
        Context $context,
        ReportFactory $reportFactory,
        JsonFactory $jsonFactory
    ) {
        parent::__construct($context);
        $this->reportFactory = $reportFactory;
        $this->jsonFactory = $jsonFactory;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        if ($this->getRequest()->getParam('isAjax')) {
            $postItems = $this->getRequest()->getParam('items', []);
            if (!count($postItems)) {
                $messages[] = __('Please correct the data sent.');
                $error = true;
            } else {
                $report = $this->reportFactory->create();
                foreach (array_keys($postItems) as $reportId) {
                    /** @var \Forix\Csp\Model\Report $report */
                    $report->load($reportId);
                    try {
                        $report->setData(array_merge($report->getData(), $postItems[$reportId]));
                        $report->save();
                    } catch (\Exception $e) {
                        $messages[] = $this->getErrorWithReportId(
                            $report,
                            __($e->getMessage())
                        );
                        $error = true;
                    }
                }
            }
        }

        return $resultJson->setData([
                                        'messages' => $messages,
                                        'error'    => $error
                                    ]);
    }

    /**
     * Add report title to error message
     *
     * @param ReportInterface $report
     * @param string $errorText
     * @return string
     */
    protected function getErrorWithReportId(ReportInterface $report, $errorText)
    {
        return '[Report ID: ' . $report->getId() . '] ' . $errorText;
    }
}
