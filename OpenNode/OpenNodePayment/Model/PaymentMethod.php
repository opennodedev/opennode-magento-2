<?php
/**
 * Copyright © OpenNode. All rights reserved.
 */
namespace OpenNode\OpenNodePayment\Model;
 
/**
 * Pay In Store payment method model
 */
class PaymentMethod extends \Magento\Payment\Model\Method\AbstractMethod
{
    /**
     * Payment code
     *
     * @var string
     */
    const PAYMENT_METHOD_OPENNODE_CODE = 'opennode';

    protected $_code = self::PAYMENT_METHOD_OPENNODE_CODE;

    /**
     * Payment Method feature
     *
     * @var bool
     */
    protected $_canAuthorize = true;

    /**
     * Payment Method status
     *
     * @var boolean
     */
    protected $_isInitializeNeeded = true;
}
