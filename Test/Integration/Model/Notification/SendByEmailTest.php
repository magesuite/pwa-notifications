<?php

namespace MageSuite\PwaNotifications\Test\Integration\Model\Notification;

class SendByEmailTest extends AbstractNotificationTest
{
    /**
     * @var \MageSuite\PwaNotifications\Model\EmailToDeviceRepository
     */
    protected $emailToDeviceRepository;

    /**
     * @var \MageSuite\PwaNotifications\Model\Notification\SendByEmail
     */
    protected $sendByEmail;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->emailToDeviceRepository = $this->objectManager->create(\MageSuite\PwaNotifications\Model\EmailToDeviceRepository::class);
        $this->sendByEmail = $this->objectManager->create(\MageSuite\PwaNotifications\Model\Notification\SendByEmail::class);
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testItPublishesMessagesToQueue()
    {
        $firstDeviceId = $this->deviceHelper->createDevice('firstEndpointTest');

        $this->emailToDeviceRepository->save('test@example.com', $firstDeviceId);

        $notification = $this->notificationFactory->create();
        $notification->setBody('test message');

        $this->sendByEmail->execute('test@example.com', $notification);

        $messages = $this->getMessages();

        $this->assertCount(1, $messages);
        $this->assertEquals($firstDeviceId, $messages[0]->device_id);
        $this->assertEquals('test message', $messages[0]->body);
    }
}
