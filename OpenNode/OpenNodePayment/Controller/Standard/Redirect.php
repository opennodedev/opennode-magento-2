<?php
/**
 * Copyright © OpenNode. All rights reserved.
 */
namespace OpenNode\OpenNodePayment\Controller\Standard;

use OpenNode\Merchant\Charge as OpenNodeCharge;

/**
 * Redirect class
 */
class Redirect extends \Magento\Framework\App\Action\Action
{
    /**
     * $_coreRegistry variable
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * $_checkoutSession variable
     *
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * $_orderFactory variable
     *
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $_orderFactory;

    /**
     * $helperData variable
     *
     * @var \OpenNode\OpenNodePayment\Helper\Data
     */
    protected $helperData;

    /**
     * $resultRedirectFactory variable
     *
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * __construct function
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory
     * @param \OpenNode\OpenNodePayment\Helper\Data $helper
     */
	public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory,
        \OpenNode\OpenNodePayment\Helper\Data $helper
    )
	{
        $this->helperData = $helper;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->_checkoutSession = $checkoutSession;
        $this->_orderFactory = $orderFactory;
		return parent::__construct($context);
	}

    /**
     * execute function
     *
     * @return void
     */
    public function execute()
    {
        $order = $this->getOrder();

        $this->_coreRegistry->register('order', $this->getOrder());

        $this->helperData->initConfig();

        $billingAddress = $order->getBillingAddress();

        $params = [
            'order_id'=> strval($order->getIncrementId()),
            'description'=> strval($order->getIncrementId()),
            'amount'=> floatval($order->getGrandTotal()),
            'currency'=> $order->getOrderCurrencyCode(),
            'callback_url'=> $this->helperData->getCallBackUrl($order->getIncrementId()),
            'success_url'=> $this->helperData->getSuccessUrl($order->getIncrementId()),
            'customer_name'=> $billingAddress->getFirstName(),
            'customer_email'=> $order->getCustomerEmail(),
        ];

        $opennode_order = OpenNodeCharge::create($params);
        $opennode_order_id = $opennode_order->id;

        // Set charged id as additional info
        $payment = $order->getPayment();
        $additionalInfo = $payment->getAdditionalInformation();
        $additionalInfo['charged_id'] = $opennode_order_id;
        $payment->setAdditionalInformation($additionalInfo);
        $payment->save();
        

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setUrl($this->helperData->getRedirectUrl().$opennode_order_id);
        return $resultRedirect;
    }

    /**
     * Get order object
     *
     * @return \Magento\Sales\Model\Order
     */
    protected function getOrder()
    {
        return $this->_orderFactory->create()->loadByIncrementId(
            $this->_checkoutSession->getLastRealOrderId()
        );
    }
}
