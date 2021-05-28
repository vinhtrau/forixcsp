<?php
/**
 * Created by: Bruce
 * Date: 26/05/2021
 * Time: 17:27
 */

namespace Forix\Csp\Model\ResourceModel;

class Report extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb{
    /**
     * construct
     * @return void
     */
    protected function _construct(){
        $this->_init('forix_csp_collector', 'id');
    }

    public function massDelete($ids)
    {
        if(empty($ids)){
            //Delete All
            $this->getConnection()->delete($this->getMainTable());
            return;
        }
        $this->getConnection()->delete($this->getMainTable(), [$this->getIdFieldName() . ' IN (?)' => $ids]);
    }
    public function massStatus($ids, $status)
    {
        if(empty($ids)){
            //Update All
            $this->getConnection()->update($this->getMainTable(), ['is_allowed' => $status]);
            return;
        }
        $this->getConnection()->update($this->getMainTable(), ['is_allowed' => $status], [$this->getIdFieldName() . ' IN (?)' => $ids]);
    }
}
