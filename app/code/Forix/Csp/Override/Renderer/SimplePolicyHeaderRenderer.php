<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Forix\Csp\Override\Renderer;

use Forix\Csp\Helper\Config;
use Magento\Csp\Api\Data\ModeConfiguredInterface;
use Magento\Csp\Api\Data\PolicyInterface;
use Magento\Csp\Api\ModeConfigManagerInterface;
use Magento\Csp\Api\PolicyRendererInterface;
use Magento\Csp\Model\Policy\SimplePolicyInterface;
use Magento\Framework\App\Response\HttpInterface as HttpResponse;

/**
 * Renders a simple policy as a "Content-Security-Policy" header.
 */
class SimplePolicyHeaderRenderer extends \Magento\Csp\Model\Policy\Renderer\SimplePolicyHeaderRenderer
{
    /**
     * @var Config
     */
    private $configHelper;

    /**
     * @param ModeConfigManagerInterface $modeConfig
     */
    public function __construct(
        Config $configHelper
    ){
        $this->configHelper = $configHelper;
    }

    /**
     * @inheritDoc
     */
    public function render(PolicyInterface $policy, HttpResponse $response): void
    {
        if ($this->configHelper->isEnableReport()) {
            $header = 'Content-Security-Policy-Report-Only';
        } else {
            $header = 'Content-Security-Policy';
        }
        $value = $policy->getId() .' ' .$policy->getValue() .';';
        if ($this->configHelper->getReportUri() && !$response->getHeader('Report-To')) {
            $reportToData = [
                'group' => 'reportCsp',
                'max_age' => 10886400,
                'endpoints' => [
                    ['url' => $this->configHelper->getReportUri()]
                ]
            ];
            $value .= ' report-uri ' .$this->configHelper->getReportUri() .';';
//            $value .= ' report-to '. $reportToData['group'] .';';
            $response->setHeader('Report-To', json_encode($reportToData), true);
        }
        if ($existing = $response->getHeader($header)) {
            $value = $value .' ' .$existing->getFieldValue();
        }
        $response->setHeader($header, $value, true);
    }

    /**
     * @inheritDoc
     */
    public function canRender(PolicyInterface $policy): bool
    {
        return true;
    }
}
