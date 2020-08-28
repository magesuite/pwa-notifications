<?php

namespace MageSuite\PwaNotifications\Test\Integration\Observer\Customer;

class LinkCustomerWithDeviceTest extends \PHPUnit\Framework\TestCase
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
    protected function setUp(): void
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->deviceHelper = $this->objectManager->get(\MageSuite\PwaNotifications\Test\Integration\DeviceHelper::class);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testItLinksCustomerWithDeviceWhenLoggedIn()
    {
        $deviceId = $this->deviceHelper->createDevice('endpoint');

        /** @var \Magento\Customer\Model\Session $session */
        $session = $this->objectManager->get(\Magento\Customer\Model\Session::class);
        $session->loginById(1);

        $emails = $this->objectManager->get(\MageSuite\PwaNotifications\Model\EmailToDeviceRepository::class)
            ->getEmailsByDeviceId($deviceId);
        $customers = $this->objectManager->get(\MageSuite\PwaNotifications\Model\CustomerToDeviceRepository::class)
            ->getCustomersByDeviceId($deviceId);

        $this->assertEquals(['customer@example.com'], $emails);
        $this->assertEquals([1], $customers);
    }
}
