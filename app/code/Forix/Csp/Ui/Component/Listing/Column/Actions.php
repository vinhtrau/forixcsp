<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingArea
 */


namespace Forix\Csp\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class Actions extends Column
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->urlBuilder = $urlBuilder;
    }


    /**
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $name = $this->getData('name');
                $indexField = $this->getData('config/indexField');

                if (isset($item[$indexField])) {
                    $item[$name]['edit'] = [
                        'href' => $this->urlBuilder->getUrl(
                            'cspreport/report/edit',
                            ['id' => $item[$indexField]]
                        ),
                        'label' => __('Edit')
                    ];

                    $item[$name]['delete'] = [
                        'href' => $this->urlBuilder->getUrl(
                            'cspreport/report/delete',
                            ['id' => $item[$indexField]]
                        ),
                        'label' => __('Delete'),
                        'confirm' => [
                            'title' => __('Delete rule ID %1', ['${ $.$data.id }']),
                            'message' => __(
                                'Are you sure you want to delete "%1" "%2"?',
                                ['${ $.$data.host }','${ $.$data.directive }']
                            ),
                            '__disableTmpl' => ['title' => false, 'message' => false],
                        ],
                        'post' => true
                    ];

                    $newStatus = $item['is_allowed'] == 1 ? "Pending" : "Approve";
                    $item[$name]['toggle'] = [
                        'href' => $this->urlBuilder->getUrl(
                            'cspreport/report/switchStatus' ?: '#',
                            ['id' => $item[$indexField]]
                        ),
                        'label' => __('Switch Status'),
                        'confirm' => [
                            'title' => __('Switch rule ID %1', ['${ $.$data.id }']),
                            'message' => __(
                                'Are you sure you want to set rule as %1?',
                                [$newStatus]
                            ),
                            '__disableTmpl' => ['title' => false, 'message' => false],
                        ],
                    ];
                }
            }
        }
        return $dataSource;
    }
}
