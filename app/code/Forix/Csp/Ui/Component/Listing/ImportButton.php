<?php
/**
 * Created by: Bruce
 * Date: 27/05/2021
 * Time: 16:46
 */

namespace Forix\Csp\Ui\Component\Listing;


use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class ImportButton implements ButtonProviderInterface{

    protected $helper;
    private $urlBuilder;

    public function __construct(
        UrlInterface $urlBuilder
    ){
        $this->helper     = ObjectManager::getInstance()->get(\Forix\Csp\Helper\ImportData::class);
        $this->urlBuilder = $urlBuilder;
    }


    public function getButtonData(){
        try{
            $list = $this->helper->getReportedList();
        }catch(NotFoundException $e){
            return [];
        }
        if(empty($list)){
            return [];
        }

        return [
            'label'      => __('Import Rules'),
            'class'      => 'primary',
            'on_click'   => sprintf("location.href = '%s';", $this->urlBuilder->getUrl('cspreport/report/collectGet')),
            'sort_order' => 0
        ];
    }

}
