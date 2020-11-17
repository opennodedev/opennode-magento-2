<?php
/**
 * Copyright © OpenNode. All rights reserved.
 */
namespace OpenNode\OpenNodePayment\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\UrlInterface;
use OpenNode\OpenNode;

/**
 * Data class
 */
class Data extends AbstractHelper
{
    // Status
    const OPENNODE_ORDER_STATUS_PROCESSING = 'processing';

    const OPENNODE_ORDER_STATUS_PAID = 'paid';

    const OPENNODE_ORDER_STATUS_REFUNDED = 'refunded';

    const OPENNODE_ORDER_STATUS_EXPIRED = 'expired';

    const XML_PATH = 'payment/opennode/';

    const XML_TITLE = 'title';

    const XML_API_KEY = 'api_key';

    const XML_REDIRECT_URL = 'redirect_url';

    const CALL_BACK_URL = 'opennodepayment/standard/webhook';

    //const SUCCESS_URL = 'checkout/onepage/success';

    const SUCCESS_URL = 'opennodepayment/standard/response';

    const ENVIRONMENT_DEV = 'dev';

    const ENVIRONMENT_LIVE = 'live';

    /**
     * $scopeConfig variable
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * $urlBuilder variable
     *
     * @var \Magento\Framework\App\Helper\Context
     */
    protected $urlBuilder;

    /**
     * __construct function
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->urlBuilder = $objectManager->create('Magento\Framework\UrlInterface');

        $this->scopeConfig = $scopeConfig;
        parent::__construct($context);
    }

    /**
     * getConfigValue function
     *
     * @param String $code
     * @param \Magento\Store\Model\ScopeInterface $storeScope
     * @return void
     */
    public function getConfigValue($code,$storeScope) {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH.$code, $storeScope);
    }

    /**
     * getEncodeValue function
     *
     * @param String $value
     * @return void
     */
    public function getEncodeValue($value) {
        return base64_encode($value);
    }

    /**
     * getDecodeValue function
     *
     * @param String $value
     * @return void
     */
    public function getDecodeValue($value) {
        return base64_decode($value);
    }

    /**
     * getCallBackUrl function
     *
     * @param String $orderId
     * @return void
     */
    public function getCallBackUrl($orderId) {
        return $this->urlBuilder->getUrl(self::CALL_BACK_URL);
    }

    /**
     * getSuccessUrl function
     *
     * @param String $orderId
     * @return void
     */
    public function getSuccessUrl($orderId) {
        $queryParams = [
            'order_id' => $this->getEncodeValue($orderId),
            'api_key' => $this->getEncodeValue($this->getApiKey())
        ];
        return $this->urlBuilder->getUrl(self::SUCCESS_URL, ['_current' => true,'_use_rewrite' => true, '_query' => $queryParams]);
    }

    /**
     * getTitle function
     *
     * @return void
     */
    public function getTitle() {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->getConfigValue(self::XML_TITLE, $storeScope);
    }

    /**
     * getApiKey function
     *
     * @return void
     */
    public function getApiKey() {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->getConfigValue(self::XML_API_KEY, $storeScope);
    }

    /**
     * getRedirectUrl function
     *
     * @return void
     */
    public function getRedirectUrl() {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->getConfigValue(self::XML_REDIRECT_URL, $storeScope);
    }

    /**
     * getEnvironment function
     *
     * @return void
     */
    public function getEnvironment() {
        $redirectUrl = $this->getRedirectUrl();
        if(strpos($redirectUrl, self::ENVIRONMENT_DEV) === false) {
            return self::ENVIRONMENT_LIVE;
        }
        return self::ENVIRONMENT_DEV;
    }

    /**
     * initConfig function
     *
     * @return void
     */
    public function initConfig() {
        $configData=[
            'auth_token'=>$this->getApiKey(),
            'environment'=>$this->getEnvironment(),
            'user_agent'=>'OpenNode Gateway',
            'curlopt_ssl_verifypeer'=>false
        ];

        return OpenNode::config($configData);
    }
}
