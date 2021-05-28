<?php
/**
 * Created by: Bruce
 * Date: 26/05/2021
 * Time: 17:05
 */
namespace Forix\Csp\Block\Adminhtml;

class Report extends \Magento\Backend\Block\Widget\Grid\Container{
    protected function _construct()
    {
        $this->_controller = 'adminhtml_report';
        $this->_blockGroup = 'Forix_Csp';
        $this->_headerText = __('Report');
        $this->_addButtonLabel = __('Add new rule');
        parent::_construct();
    }
}
