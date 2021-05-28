<?php
/**
 * Created by: Bruce
 * Date: 26/05/2021
 * Time: 17:44
 */

namespace Forix\Csp\Model;
use Magento\Framework\App\ObjectManager;

class Report extends \Magento\Framework\Model\AbstractModel{

    protected $dateTime;

    protected function _construct(){
        $this->_init(\Forix\Csp\Model\ResourceModel\Report::class);
        $this->setIdFieldName('id');
        $this->dateTime = ObjectManager::getInstance()->get(\Magento\Framework\Stdlib\DateTime\DateTime::class);
    }

    public function beforeSave(){
        if(!$this->getId()){
            $this->setCreatedAt(strftime('%Y-%m-%d %H:%M:%S', $this->dateTime->gmtTimestamp()));
        }
        return $this;
    }
}
