<?php

namespace MageSuite\PwaNotifications\Test\Integration\Plugin\Magento\Sales\Model\OrderManagement;

class LinkDeviceWithPlacedOrderTest extends \PHPUnit\Framework\TestCase
{
    /** @var \Magento\TestFramework\ObjectManager */
    protected $objectManager;

    /**
     * @var \MageSuite\PwaNotifications\Helper\Session
     */
    protected $sessionHelper;

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
        $this->sessionHelper = $this->objectManager->get(\MageSuite\PwaNotifications\Helper\Session::class);
        $this->deviceHelper = $this->objectManager->get(\MageSuite\PwaNotifications\Test\Integration\DeviceHelper::class);
        $this->orderHelper = $this->objectManager->get(\MageSuite\PwaNotifications\Test\Integration\OrderHelper::class);
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoDataFixture Magento/Sales/_files/guest_quote_with_addresses.php
     */
    public function testItLinksOrderDataWithDevice()
    {
        $deviceId = $this->deviceHelper->createDevice('endpoint');
        $order = $this->orderHelper->placeOrder('guest_quote');

        $emails = $this->objectManager->get(\MageSuite\PwaNotifications\Model\EmailToDeviceRepository::class)
            ->getEmailsByDeviceId($deviceId);
        $orders = $this->objectManager->get(\MageSuite\PwaNotifications\Model\OrderToDeviceRepository::class)
            ->getOrdersByDeviceId($deviceId);

        $this->assertEquals(['some_email@mail.com'], $emails);
        $this->assertEquals([$order->getId()], $orders);
    }
}
