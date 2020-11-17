<?php
/**
 * Copyright © OpenNode. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace OpenNode\OpenNodePayment\Block;

/**
 * Redirect class
 */
class Redirect extends \Magento\Framework\View\Element\Template
{
    /**
     * $_coreRegistry variable
     *
     * @var  \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * $_helper variable
     *
     * @var \OpenNode\OpenNodePayment\Helper\Data
     */
    protected $_helper;

    /**
     * $_urlHelper variable
     *
     * @var \Magento\Framework\Url
     */
    protected $_urlHelper;

    /**
     * __construct function
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \OpenNode\OpenNodePayment\Helper\Data $helper
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\Url $urlHelper
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \OpenNode\OpenNodePayment\Helper\Data $helper,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Url $urlHelper
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_helper = $helper;
        $this->_urlHelper = $urlHelper;
        parent::__construct($context);
    }

    /**
     * Get order
     *
     * @return void
     */
    public function getOrder(){
        return $this->_coreRegistry->registry('order');
    }
    
    /**
     * Get order increment id
     *
     * @return void
     */
    public function getOrderIncrementId(){
        return $this->getOrder()->getIncrementId();
    }

    /**
     * Get order currency
     *
     * @return void
     */
    public function getOrderCurrency(){
        return $this->getOrder()->getOrderCurrencyCode();
    }

    /**
     * Get order grand total
     *
     * @return void
     */
    public function getOrderGrandTotal(){
        return $this->getOrder()->getGrandTotal();
    }

    /**
     * get order email
     *
     * @return void
     */
    public function getOrderEmail(){
        return $this->getOrder()->getCustomerEmail();
    }

    /**
     * Get order customer name
     *
     * @return void
     */
    public function getOrderCustomerName(){

        $billingAddress = $this->getOrder()->getBillingAddress();

        return $billingAddress->getFirstName().' '.$billingAddress->getLastname();
    }

    /**
     * Get api key
     *
     * @return void
     */
    public function getApiKey(){
        return $this->_helper->getApiKey();
    }

    /**
     * Get redirect url
     *
     * @return void
     */
    public function getRedirectUrl(){
        return $this->_helper->getRedirectUrl().$this->getOrderIncrementId();
    }

    /**
     * get callback url
     *
     * @return void
     */
    public function getCallBackUrl(){
        return $this->_helper->getCallBackUrl();
    }

    /**
     * get success url
     *
     * @return void
     */
    public function getSuccessUrl(){
        return $this->_helper->getSuccessUrl();
    }
    
}
