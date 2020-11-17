<?php
/**
 * Copyright © OpenNode. All rights reserved.
 */
namespace OpenNode\OpenNodePayment\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Framework\UrlInterface as UrlInterface;
use Magento\Framework\View\Asset\Repository;

/**
 * OpenNodePaymentConfigProvider class
 */
class OpenNodePaymentConfigProvider implements ConfigProviderInterface
{
    /**
     * $method variable
     *
     * @var PaymentHelper
     */
    protected $method;
    
    /**
     * $urlBuilder variable
     *
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * $repository variable
     *
     * @var Repository
     */
    protected $repository;

    /**
     * __construct function
     *
     * @param PaymentHelper $paymentHelper
     * @param UrlInterface $urlBuilder
     * @param Repository $repository
     */
    public function __construct(
        PaymentHelper $paymentHelper,
        UrlInterface $urlBuilder,
        Repository $repository
    ) {
        $this->method = $paymentHelper->getMethodInstance(\OpenNode\OpenNodePayment\Model\PaymentMethod::PAYMENT_METHOD_OPENNODE_CODE);
        $this->urlBuilder = $urlBuilder;
        $this->repository = $repository;
    }

    /**
     * getConfig function
     *
     * @return void
     */
    public function getConfig()
    {
        return $this->method->isAvailable() ? [
            'payment' => [
                \OpenNode\OpenNodePayment\Model\PaymentMethod::PAYMENT_METHOD_OPENNODE_CODE => [
                    'code'=> \OpenNode\OpenNodePayment\Model\PaymentMethod::PAYMENT_METHOD_OPENNODE_CODE,
                    'redirectUrl' => $this->urlBuilder->getUrl('opennodepayment/standard/redirect', ['_secure' => true])
                ]
            ]
        ] : [];
    }
}
