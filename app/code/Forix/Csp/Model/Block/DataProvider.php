<?php
/**
 * Created by: Bruce
 * Date: 27/05/2021
 * Time: 14:01
 */

namespace Forix\Csp\Model\Block;

use Forix\Csp\Model\ResourceModel\Report\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Magento\Ui\DataProvider\ModifierPoolDataProvider;

class DataProvider extends ModifierPoolDataProvider{
    /**
     * @var \Magento\Cms\Model\ResourceModel\Block\Collection
     */
    protected $collection;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * Constructor
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $reportCollectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     * @param PoolInterface|null $pool
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $reportCollectionFactory,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = [],
        PoolInterface $pool = null
    ) {
        $this->collection = $reportCollectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data, $pool);
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        /** @var \Forix\Csp\Model\Report $report */
        foreach ($items as $report) {
            $this->loadedData[$report->getId()] = $report->getData();
        }

        $data = $this->dataPersistor->get('csp_report');
        if (!empty($data)) {
            $report = $this->collection->getNewEmptyItem();
            $report->setData($data);
            $this->loadedData[$report->getId()] = $report->getData();
            $this->dataPersistor->clear('csp_report');
        }

        return $this->loadedData;
    }
}
