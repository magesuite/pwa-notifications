<?php

namespace MageSuite\PwaNotifications\Test\Integration\Observer;

class LinkLastOrderWithDeviceIdTest extends \PHPUnit\Framework\TestCase
{
    /** @var \Magento\TestFramework\ObjectManager */
    protected $objectManager;

    /**
     * @var \Magento\Quote\Api\CartManagementInterface
     */
    protected $cartManagement;

    /**
     * @var \MageSuite\PwaNotifications\Model\DeviceInformationManagement
     */
    protected $deviceInformationManagement;

    /**
     * @var \Magento\Quote\Model\QuoteIdMaskFactory
     */
    protected $quoteIdMaskFactory;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->cartManagement = $this->objectManager->create(\Magento\Quote\Api\CartManagementInterface::class);
        $this->deviceInformationManagement = $this->objectManager->create(\MageSuite\PwaNotifications\Model\DeviceInformationManagement::class);
        $this->quoteIdMaskFactory = $this->objectManager->get(\Magento\Quote\Model\QuoteIdMaskFactory::class);
    }

    /**
     * @magentoDataFixture Magento/Sales/_files/guest_quote_with_addresses.php
     */
    public function testItLinksOrderDataWithDevice(): void
    {
        $order = $this->placeOrder('guest_quote');

        $encryptionKeys = $this->objectManager->create(\MageSuite\PwaNotifications\Api\Data\EncryptionKeysInterface::class);
        $encryptionKeys->setP256dh('p256dhTest');
        $encryptionKeys->setAuth('authTest');

        $deviceId = $this->deviceInformationManagement->save('endpointTest', $encryptionKeys);

        $emails = $this->objectManager->get(\MageSuite\PwaNotifications\Model\EmailToDeviceRepository::class)
            ->getEmailsByDeviceId($deviceId);
        $orders = $this->objectManager->get(\MageSuite\PwaNotifications\Model\OrderToDeviceRepository::class)
            ->getOrdersByDeviceId($deviceId);

        $this->assertEquals(['some_email@mail.com'], $emails);
        $this->assertEquals([$order->getId()], $orders);
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
