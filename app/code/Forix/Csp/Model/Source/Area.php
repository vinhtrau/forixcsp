<?php
/**
 * Created by: Bruce
 * Date: 28/05/2021
 * Time: 15:15
 */

namespace Forix\Csp\Model\Source;
use Magento\Framework\Data\OptionSourceInterface;

class Area implements OptionSourceInterface{
    public function toOptionArray(){
        return [
            ["label" => "Frontend", "value" => "frontend"],
            [ "label" => "Admin", "value" => "admin"]
        ];
    }
}
