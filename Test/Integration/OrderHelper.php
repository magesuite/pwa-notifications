<?php

namespace MageSuite\PwaNotifications\Test\Integration;

class OrderHelper
{
    /** @var \Magento\TestFramework\ObjectManager */
    protected $objectManager;

    /**
     * @var \Magento\Quote\Api\CartManagementInterface
     */
    protected $cartManagement;

    /**
     * @var \Magento\Quote\Model\QuoteIdMaskFactory
     */
    protected $quoteIdMaskFactory;

    public function __construct()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->cartManagement = $this->objectManager->create(\Magento\Quote\Api\CartManagementInterface::class);
        $this->quoteIdMaskFactory = $this->objectManager->get(\Magento\Quote\Model\QuoteIdMaskFactory::class);
    }

    public function placeOrder($quoteIdentifier)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->objectManager->create(\Magento\Quote\Model\Quote::class);
        $quote->load($quoteIdentifier, 'reserved_order_id');

        $checkoutSession = $this->objectManager->get(\Magento\Checkout\Model\Session::class);
        $checkoutSession->setQuoteId($quote->getId());

        /** @var \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create();
        $quoteIdMask->load($quote->getId(), 'quote_id');
        $cartId = $quoteIdMask->getMaskedId();

        /** @var \Magento\Quote\Api\GuestCartManagementInterface $cartManagement */
        $cartManagement = $this->objectManager->get(\Magento\Quote\Api\GuestCartManagementInterface::class);
        $orderId = $cartManagement->placeOrder($cartId);
        $order = $this->objectManager->get(\Magento\Sales\Model\OrderRepository::class)->get($orderId);

        return $order;
    }
}
