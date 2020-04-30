<?php

namespace MageSuite\PwaNotifications\Test\Integration\Model\Notification;

class SendByOrderTest extends AbstractNotificationTest
{
    /**
     * @var \MageSuite\PwaNotifications\Model\EmailToDeviceRepository
     */
    protected $emailToDeviceRepository;

    /**
     * @var \MageSuite\PwaNotifications\Model\Notification\SendByOrder
     */
    protected $sendByOrder;

    /**
     * @var \MageSuite\PwaNotifications\Test\Integration\OrderHelper
     */
    protected $orderHelper;

    /**
     * @var \MageSuite\PwaNotifications\Model\OrderToDeviceRepository
     */
    protected $orderToDeviceRepository;

    protected function setUp()
    {
        parent::setUp();

        $this->emailToDeviceRepository = $this->objectManager->create(\MageSuite\PwaNotifications\Model\EmailToDeviceRepository::class);
        $this->orderToDeviceRepository = $this->objectManager->create(\MageSuite\PwaNotifications\Model\OrderToDeviceRepository::class);
        $this->sendByOrder = $this->objectManager->create(\MageSuite\PwaNotifications\Model\Notification\SendByOrder::class);
        $this->orderHelper = $this->objectManager->create(\MageSuite\PwaNotifications\Test\Integration\OrderHelper::class);
    }

    /**
     * @magentoDataFixture Magento/Sales/_files/guest_quote_with_addresses.php
     * @magentoDbIsolation enabled
     */
    public function testItPublishesMessagesToQueue()
    {
        $order = $this->orderHelper->placeOrder('guest_quote');

        $firstDeviceId = $this->deviceHelper->createDevice('firstEndpointTest');
        $secondDeviceId = $this->deviceHelper->createDevice('secondEndpointTest');

        $this->emailToDeviceRepository->save('customer@example.com', $firstDeviceId);
        $this->orderToDeviceRepository->save($order->getId(), $secondDeviceId);

        $this->sendByOrder->execute($order, 'test message');

        $messages = $this->getMessages();

        $this->assertCount(2, $messages);
        $this->assertEquals($firstDeviceId, $messages[0]->device_id);
        $this->assertEquals($secondDeviceId, $messages[1]->device_id);
        $this->assertEquals('test message', $messages[0]->message);
        $this->assertEquals('test message', $messages[1]->message);
    }
}
