<?php
/**
 * Copyright © OpenNode. All rights reserved.
 */
namespace OpenNode\OpenNodePayment\Controller\Standard;

use OpenNode\Merchant\Charge as OpenNodeCharge;

/**
 * Webhook class
 */
class Webhook extends \Magento\Framework\App\Action\Action
{
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
     * $response variable
     *
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $response;

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
     * @param \Magento\Framework\App\ResponseInterface $response
     */
	public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \OpenNode\OpenNodePayment\Model\ManageStatus $openNodeStatus,
        \OpenNode\OpenNodePayment\Helper\Data $helper,
        \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Url $urlHelper,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Framework\App\ResponseInterface $response
    )
	{

        $this->openNodeStatus = $openNodeStatus;
        $this->helper = $helper;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->messageManager = $messageManager;
        $this->_urlHelper = $urlHelper;
        $this->orderFactory = $orderFactory;
        $this->response = $response;

        parent::__construct($context);

        return $this->execute();
	}

    public function execute()
    {
        try {

            if($this->checkSingneture($_REQUEST)){

                $responseContent = [
                    'success' => true,
                    'message' => 'Order status updated.'
                ];

                $chargedId = $_REQUEST['id'];

                $this->helper->initConfig();
                $responseOpenNode = OpenNodeCharge::find($_REQUEST['id']);

                $orderId = $responseOpenNode->order_id;
                $status = $responseOpenNode->status;


                switch ($status) {

                    case $this->helper::OPENNODE_ORDER_STATUS_PAID:
                        $this->openNodeStatus->updateOrderAsPaidStatus($orderId, $chargedId);
                        break;

                    case $this->helper::OPENNODE_ORDER_STATUS_PROCESSING:
                        $this->openNodeStatus->updateOrderAsProcessingStatus($orderId, $chargedId);
                        break;

                    case $this->helper::OPENNODE_ORDER_STATUS_REFUNDED:
                        $this->openNodeStatus->updateOrderAsRefundedStatus($orderId, $chargedId);
                        break;

                    case $this->helper::OPENNODE_ORDER_STATUS_EXPIRED:
                        $this->openNodeStatus->updateOrderAsExpiredStatus($orderId, $chargedId);
                        break;
                }

            }

            $resultJson = $this->response->create(ResultFactory::TYPE_JSON);
            $resultJson->setHttpResponseCode(200);
            $resultJson->setData($responseContent);
            return $resultJson;

        }catch(\Exception $e) {
            $responseContent = [
                'success' => false,
                'message' => $e->getMessage(),
            ];
            $resultJson = $this->response->create(ResultFactory::TYPE_JSON);
            $resultJson->setHttpResponseCode(500);
            $resultJson->setData($responseContent);
            return $resultJson;

        }
    }

    /**
     * Check singneture
     *
     * @param [type] $params
     * @return void
     */
    protected function checkSingneture($params)
    {
        $signeture = hash_hmac('sha256', $params['id'], $this->helper->getApiKey());
        if($signeture === $params['hashed_order']) {
            return true;
        }
        return false;
    }
}
