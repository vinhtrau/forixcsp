<?php
/**
 * Created by: Bruce
 * Date: 07/13/20
 * Time: 13:23
 */

namespace Forix\Csp\Block\Adminhtml;

use Magento\Backend\Block\Template;
use Forix\Csp\Helper\ImportData;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Collector extends \Magento\Config\Block\System\Config\Form\Field{

    protected $_template = 'Forix_Csp::collector.phtml';

    protected $helper;

    public function __construct(
        Template\Context $context,
        ImportData $helper,
        array $data = []){
        $this->helper = $helper;
        parent::__construct($context, $data);
    }

    public function getReportedList(){
        try{
            return $this->helper->getReportedList();
        }catch(\Exception $e){
            return [];
        }

    }
    public function getSubmitFormUrl(){
        return $this->_urlBuilder->getUrl("cspreport/report/collect");
    }
    public function getLoadListUrl(){
        return $this->_urlBuilder->getUrl("cspreport/report/reportedList");
    }
    protected function _getElementHtml(AbstractElement $element)
    {
        return $this->toHtml();
    }
}
