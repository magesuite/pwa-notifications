<?php

namespace MageSuite\PwaNotifications\Test\Integration\Observer;

class LinkLastOrderAfterDeviceIsCreatedTest extends \PHPUnit\Framework\TestCase
{
    /** @var \Magento\TestFramework\ObjectManager */
    protected $objectManager;

    /**
     * @var \MageSuite\PwaNotifications\Test\Integration\DeviceHelper
     */
    protected $deviceHelper;

    /**
     * @var \MageSuite\PwaNotifications\Test\Integration\OrderHelper
     */
    protected $orderHelper;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->deviceHelper = $this->objectManager->get(\MageSuite\PwaNotifications\Test\Integration\DeviceHelper::class);
        $this->orderHelper = $this->objectManager->get(\MageSuite\PwaNotifications\Test\Integration\OrderHelper::class);
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoDataFixture Magento/Sales/_files/guest_quote_with_addresses.php
     */
    public function testItLinksOrderDataWithDevice(): void
    {
        $order = $this->orderHelper->placeOrder('guest_quote');
        $deviceId = $this->deviceHelper->createDevice('endpoint');

        $emails = $this->objectManager->get(\MageSuite\PwaNotifications\Model\EmailToDeviceRepository::class)
            ->getEmailsByDeviceId($deviceId);
        $orders = $this->objectManager->get(\MageSuite\PwaNotifications\Model\OrderToDeviceRepository::class)
            ->getOrdersByDeviceId($deviceId);

        $this->assertEquals(['some_email@mail.com'], $emails);
        $this->assertEquals([$order->getId()], $orders);
    }
}
