<?php
/**
 * ClassyLlama_AvaTax
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @copyright  Copyright (c) 2018 Avalara, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace ClassyLlama\AvaTax\Block\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use ClassyLlama\AvaTax\Framework\Interaction\Rest\Company as RestCompany;
use ClassyLlama\AvaTax\Model\Logger\AvaTaxLogger;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class AccountAddExemptionZone
 * @package ClassyLlama\AvaTax\Block\ViewModel
 */
class AccountAddExemptionZone implements ArgumentInterface
{
    /**#@+
     * XML paths to configuration.
     */
    public const XML_PATH_CERTCAPTURE_AUTO_VALIDATION = 'tax/avatax_certificate_capture/disable_certcapture_auto_validation';
    /**#@-*/

    /**
     * @var RestCompany
     */
    protected $companyRest;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var AvaTaxLogger
     */
    private $avaTaxLogger;

    /**
     * AccountAddExemptionZone constructor.
     * @param RestCompany $companyRest
     * @param ScopeConfigInterface $scopeConfig
     * @param AvaTaxLogger $avaTaxLogger
     */
    public function __construct(RestCompany $companyRest, ScopeConfigInterface $scopeConfig, AvaTaxLogger $avaTaxLogger)
    {
        $this->companyRest = $companyRest;
        $this->scopeConfig = $scopeConfig;
        $this->avaTaxLogger = $avaTaxLogger;
    }

    /**
     * @return false|string
     */
    public function getCertificateExposureZonesJsConfig()
    {
        try {
            $zones = $this->companyRest->getCertificateExposureZones();
            return json_encode(array_map(function ($zone) {
                return $zone->name;
            }, $zones->value));
        } catch (\Throwable $exception) {
            $this->avaTaxLogger->error($exception->getMessage(), [
                'class' => self::class,
                'trace' => $exception->getTraceAsString()
            ]);
        }
        return '';
    }

    /**
     * @return string
     */
    public function isCertificatesAutoValidationDisabled(): string
    {
        return (string)(int)$this->scopeConfig->isSetFlag(
            self::XML_PATH_CERTCAPTURE_AUTO_VALIDATION,
            ScopeInterface::SCOPE_STORE
        );
    }
}
