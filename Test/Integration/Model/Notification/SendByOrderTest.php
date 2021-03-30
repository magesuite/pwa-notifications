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

    /**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    protected $sessionManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->emailToDeviceRepository = $this->objectManager->create(\MageSuite\PwaNotifications\Model\EmailToDeviceRepository::class);
        $this->orderToDeviceRepository = $this->objectManager->create(\MageSuite\PwaNotifications\Model\OrderToDeviceRepository::class);
        $this->sendByOrder = $this->objectManager->create(\MageSuite\PwaNotifications\Model\Notification\SendByOrder::class);
        $this->orderHelper = $this->objectManager->create(\MageSuite\PwaNotifications\Test\Integration\OrderHelper::class);
        $this->sessionManager = $this->objectManager->create(\Magento\Framework\Session\SessionManagerInterface::class);
    }

    /**
     * @magentoDataFixture Magento/Sales/_files/guest_quote_with_addresses.php
     * @magentoDbIsolation enabled
     */
    public function testItPublishesMessagesToQueue()
    {
        $order = $this->orderHelper->placeOrder('guest_quote');

        $firstDeviceId = $this->deviceHelper->createDevice('firstEndpointTest');
        $this->sessionManager->setPwaDeviceId(0);
        
        $secondDeviceId = $this->deviceHelper->createDevice('secondEndpointTest');

        $this->emailToDeviceRepository->save('customer@example.com', $firstDeviceId);
        $this->orderToDeviceRepository->save($order->getId(), $secondDeviceId);

        $notification = $this->notificationFactory->create();
        $notification->setBody('test message');

        $this->sendByOrder->execute($order, $notification);

        $messages = $this->getMessages();

        $this->assertCount(2, $messages);
        $this->assertEquals($firstDeviceId, $messages[0]->device_id);
        $this->assertEquals($secondDeviceId, $messages[1]->device_id);
        $this->assertEquals('test message', $messages[0]->body);
        $this->assertEquals('test message', $messages[1]->body);
    }
}
