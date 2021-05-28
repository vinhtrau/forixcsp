<?php
/**
 * Created by: Bruce
 * Date: 27/05/2021
 * Time: 13:45
 */

namespace Forix\Csp\Block\Adminhtml\Report\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Ui\Component\Control\Container;

class DeleteButton extends GenericButton implements ButtonProviderInterface{

    public function getButtonData()
    {
        $data = [];
        if ($this->getReportId()) {
            $data = [
                'label' => __('Delete Block'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm(\'' . __(
                        'Are you sure you want to do this?'
                    ) . '\', \'' . $this->getDeleteUrl() . '\', {"data": {}})',
                'sort_order' => 20,
            ];
        }
        return $data;
    }

    /**
     * URL to send delete requests to.
     *
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', ['id' => $this->getReportId()]);
    }
}
