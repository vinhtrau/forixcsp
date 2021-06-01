<?php
/**
 * Created by: Bruce
 * Date: 27/05/2021
 * Time: 13:45
 */

namespace Forix\Csp\Block\Adminhtml\Report\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Ui\Component\Control\Container;

class SaveButton extends GenericButton implements ButtonProviderInterface{


    public function getButtonData(){
        return [
            'label'          => __('Save'),
            'class'          => 'save primary',
            'data_attribute' => [
                'mage-init' => [
                    'buttonAdapter' => [
                        'actions' => [
                            [
                                'targetName' => 'csp_report_form.csp_report_form',
                                'actionName' => 'save',
                                'params'     => [
                                    true,
                                    [
                                        'back' => 'close'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'class_name'     => Container::SPLIT_BUTTON,
            'options'        => $this->getOptions(),
        ];
    }
    private function getOptions()
    {
        $options = [
            [
                'label' => __('Save & Duplicate'),
                'id_hard' => 'save_and_duplicate',
                'data_attribute' => [
                    'mage-init' => [
                        'buttonAdapter' => [
                            'actions' => [
                                [
                                    'targetName' => 'csp_report_form.csp_report_form',
                                    'actionName' => 'save',
                                    'params' => [
                                        true,
                                        [
                                            'back' => 'duplicate'
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            [
                'id_hard' => 'save_and_continue',
                'label' => __('Save & Continue'),
                'data_attribute' => [
                    'mage-init' => [
                        'buttonAdapter' => [
                            'actions' => [
                                [
                                    'targetName' => 'csp_report_form.csp_report_form',
                                    'actionName' => 'save',
                                    'params' => [
                                        true,
                                        [
                                            'back' => 'continue'
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        return $options;
    }
}
