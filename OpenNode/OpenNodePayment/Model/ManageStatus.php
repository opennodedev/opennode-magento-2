<?php
/**
 * Copyright © OpenNode. All rights reserved.
 */
namespace OpenNode\OpenNodePayment\Model;

/**
 * ManageStatus class
 */
class ManageStatus
{
    /**
     * $_coreRegistry variable
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

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
     * $orderSender variable
     *
     * @var \Magento\Sales\Model\Order\Email\Sender\OrderSender
     */
    protected $orderSender;

    /**
     * __construct function
     *
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \OpenNode\OpenNodePayment\Helper\Data $helper
     * @param \Magento\Sales\Model\Order\Email\Sender\OrderSender $orderSender
     */
	public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \OpenNode\OpenNodePayment\Helper\Data $helper,
        \Magento\Sales\Model\Order\Email\Sender\OrderSender $orderSender
    )
	{
        $this->_coreRegistry = $coreRegistry;
        $this->_orderFactory = $orderFactory;
        $this->helperData = $helper;
        $this->orderSender = $orderSender;
    }

    /**
     * Get order object
     *
     * @return \Magento\Sales\Model\Order
     */
    protected function getOrder($orderId)
    {
        return $this->_orderFactory->create()->loadByIncrementId($orderId);
    }
    
    /**
     * updateOrderAsPaidStatus function
     *
     * @param String $orderId
     * @param String $chargedId
     * @return void
     */
    public function updateOrderAsPaidStatus($orderId, $chargedId)
    {
        $order = $this->getOrder($orderId);

        $payment = $order->getPayment();

        $payment->setTransactionId($chargedId)       
                    ->setShouldCloseParentTransaction(true)
                    ->setIsTransactionClosed(0)	
                    ->registerCaptureNotification(
                        $order->getGrandTotal(),
                        true 
                    );

        $payment->getCreatedInvoice();

        $order->setState(\Magento\Sales\Model\Order::STATE_PROCESSING);
        $order->setStatus($order::STATE_PROCESSING);
        $order->addStatusToHistory($order->getStatus(), __('Payment was successfully completed. OpenNode ID: '.$chargedId));
        $order->save();

        $this->orderSender->send($order, true);

        return;

    }

    /**
     * updateOrderAsProcessingStatus function
     *
     * @param String $orderId
     * @param String $chargedId
     * @return void
     */
    public function updateOrderAsProcessingStatus($orderId, $chargedId)
    {
        $order = $this->getOrder($orderId);
        $order->addStatusToHistory($order->getStatus(), __('Your customer initiated payment and the transaction is currently processing. OpenNode ID: '.$chargedId));
        $order->save();

        return;
    }

    /**
     * updateOrderAsExpiredStatus function
     *
     * @param String $orderId
     * @param String $chargedId
     * @return void
     */
    public function updateOrderAsExpiredStatus($orderId, $chargedId)
    {
        $order = $this->getOrder($orderId);
        $order->setState(\Magento\Sales\Model\Order::STATE_CANCELED);
        $order->setStatus($order::STATE_CANCELED);
        $order->addStatusToHistory($order->getStatus(), __('Your customer failed to complete payment within the allotted time. OpenNode ID: '.$chargedId));
        $order->save();

        return;
    }

    /**
     * updateOrderAsRefundedStatus function
     *
     * @param String $orderId
     * @param String $chargedId
     * @return void
     */
    public function updateOrderAsRefundedStatus($orderId, $chargedId)
    {
        $order = $this->getOrder($orderId);
        $order->setState(\Magento\Sales\Model\Order::STATE_CANCELED);
        $order->setStatus($order::STATE_CANCELED);
        $order->addStatusToHistory($order->getStatus(), __('Your customer underpaid and requested a refund instead of completing payment. This order is cancelled. OpenNode ID: '.$chargedId));
        $order->save();

        return;
    }
}
