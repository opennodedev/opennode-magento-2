<?php
/**
 * Copyright © OpenNode. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace OpenNode\OpenNodePayment\Controller\Standard;

use OpenNode\Merchant\Charge as OpenNodeCharge;

/**
 * Response class
 */
class Response extends \Magento\Framework\App\Action\Action
{
    const PAID_MSG = 'Your payment was successfully completed.';
    const PROCESSING_MSG = 'Your payment has been received, and processing will be complete once 1 confirmation is received from the Bitcoin blockchain (typically within 10 minutes - 2 hours)';
    const REFUND_MSG = 'Your payment has been cancelled.';
    const EXPIRED_MSG = 'Your payment has been cancelled';
    
    /**
     * $openNodeStatus variable
     *
     * @var \OpenNode\OpenNodePayment\Model\ManageStatus
     */
    protected $openNodeStatus;

    /**
     * $helper variable
     *
     * @var \OpenNode\OpenNodePayment\Helper\Data
     */
    protected $helper;

    /**
     * $resultRedirectFactory variable
     *
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * $messageManager variable
     *
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * $_urlHelper variable
     *
     * @var \Magento\Framework\Url
     */
    protected $_urlHelper;

    /**
     * $orderFactory variable
     *
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $orderFactory;

    /**
     * __construct function
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \OpenNode\OpenNodePayment\Model\ManageStatus $openNodeStatus
     * @param \OpenNode\OpenNodePayment\Helper\Data $helper
     * @param \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\Url $urlHelper
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     */
	public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \OpenNode\OpenNodePayment\Model\ManageStatus $openNodeStatus,
        \OpenNode\OpenNodePayment\Helper\Data $helper,
        \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Url $urlHelper,
        \Magento\Sales\Model\OrderFactory $orderFactory
    )
	{
        $this->openNodeStatus = $openNodeStatus;
        $this->helper = $helper;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->messageManager = $messageManager;
        $this->_urlHelper = $urlHelper;
        $this->orderFactory = $orderFactory;

		return parent::__construct($context);
	}

    /**
     * execute function
     *
     * @return void
     */
    public function execute()
    {
        $returnUrl = $this->_urlHelper->getUrl('checkout/onepage/failure');

        try {

            if(isset($_REQUEST['order_id'])) {

                $orderId =  $this->helper->getDecodeValue($_REQUEST['order_id']);
    
                $order = $this->getOrder($orderId);
                $chargedId = $this->getChargedIdFromOrder($order);

                if($chargedId !== null) {
    
                    $this->helper->initConfig();
                    $responseOpenNode = OpenNodeCharge::find($chargedId);

                    $status = $responseOpenNode->status;
                    switch ($status) {

                        case $this->helper::OPENNODE_ORDER_STATUS_PAID:
                            $returnUrl = $this->_urlHelper->getUrl('checkout/onepage/success');
                            $this->messageManager->addSuccess( __(self::PAID_MSG) );
                            break;
    
                        case $this->helper::OPENNODE_ORDER_STATUS_PROCESSING:
                            $returnUrl = $this->_urlHelper->getUrl('checkout/onepage/failure');
                            $this->messageManager->addNotice( __(self::PROCESSING_MSG) );
                            break;

                        case $this->helper::OPENNODE_ORDER_STATUS_REFUNDED:
                            $returnUrl = $this->_urlHelper->getUrl('checkout/onepage/failure');
                            $this->messageManager->addError( __(self::REFUND_MSG) );
                            break;

                        case $this->helper::OPENNODE_ORDER_STATUS_EXPIRED:
                            $returnUrl = $this->_urlHelper->getUrl('checkout/onepage/failure');
                            $this->messageManager->addError( __(self::EXPIRED_MSG) );
                            break;
                    }
    
                }
            }

        }catch(\Exception $e) {

            $returnUrl = $this->_urlHelper->getUrl('checkout/onepage/failure');
            $this->messageManager->addNotice( __(self::EXPIRED_MSG) );

        }finally{
            $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl($returnUrl);
            return $resultRedirect;

        }
    }

    /**
     * Get order object
     *
     * @return \Magento\Sales\Model\Order
     */
    protected function getOrder($orderId)
    {
        return $this->orderFactory->create()->loadByIncrementId($orderId);
    }

    /**
     * Get Charge id
     *
     * @param [type] $order
     * @return void
     */
    protected function getChargedIdFromOrder($order)
    {
        $payment = $order->getPayment();
        $additionalInfo = $payment->getAdditionalInformation();
        if(isset($additionalInfo['charged_id'])) {
            return $additionalInfo['charged_id'];
        }

        return null;
    }
}
