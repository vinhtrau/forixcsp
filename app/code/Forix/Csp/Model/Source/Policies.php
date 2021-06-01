<?php
/**
 * Created by: Bruce
 * Date: 28/05/2021
 * Time: 15:15
 */

namespace Forix\Csp\Model\Source;

use Magento\Csp\Model\Policy\FetchPolicy;
use Magento\Framework\Data\OptionSourceInterface;

class Policies implements OptionSourceInterface{
    public function toOptionArray(){
        $policies = FetchPolicy::POLICIES;
        $options  = [];
        foreach($policies as $policy){
            $options[] = ['label' => $policy, 'value' => $policy];
        }
        return $options;
    }
}
