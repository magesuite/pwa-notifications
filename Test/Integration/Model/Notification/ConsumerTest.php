<?php

namespace MageSuite\PwaNotifications\Test\Integration\Model\Notification;

class ConsumerTest extends AbstractNotificationTest
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected $subscription;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected $client;

    /**
     * @var \MageSuite\PwaNotifications\Model\Notification\Consumer
     */
    protected $consumer;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected $logger;

    public function setUp(): void
    {
        parent::setUp();

        $subscriptionFactory = $this->getMockBuilder(\MageSuite\PwaNotifications\Model\WebPush\Factory\Subscription::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->subscription = $this->getMockBuilder(\Minishlink\WebPush\Subscription::class)
            ->disableOriginalConstructor()
            ->getMock();

        $subscriptionFactory->method('create')
            ->willReturn($this->subscription);

        $clientFactory = $this->getMockBuilder(\MageSuite\PwaNotifications\Model\WebPush\Factory\Client::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->client = $this->getMockBuilder(\Minishlink\WebPush\WebPush::class)
            ->disableOriginalConstructor()
            ->getMock();

        $clientFactory->method('create')
            ->willReturn($this->client);

        $this->logger = $this->getMockBuilder(\Psr\Log\LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->consumer = $this->objectManager->create(\MageSuite\PwaNotifications\Model\Notification\Consumer::class, [
            'subscriptionFactory' => $subscriptionFactory,
            'clientFactory' => $clientFactory,
            'logger' => $this->logger
        ]);
    }

    public function testItSendsMessage()
    {
        $messageSentReport = $this->getMockBuilder(\Minishlink\WebPush\MessageSentReport::class)
            ->disableOriginalConstructor()
            ->getMock();

        $deviceId = $this->deviceHelper->createDevice('endpointTest');
        $notification = new \MageSuite\PwaNotifications\Model\Data\Notification();
        $notification->setDeviceId($deviceId);
        $notification->setBody('pwa message');

        $this->client->expects($this->once())->method('sendNotification')->with($this->subscription, $notification);
        $this->client->expects($this->once())->method('flush')->willReturn($this->createGenerator([$messageSentReport]));

        $this->consumer->process($notification);
    }

    public function testItDoesNotSendMessageWhenDeviceDoesNotExist()
    {
        $notification = new \MageSuite\PwaNotifications\Model\Data\Notification();
        $notification->setDeviceId(1000000000000);
        $notification->setBody('pwa message');

        $this->logger->expects($this->once())->method('error')->with('Device with id 1000000000000 does not exist.');
        $this->client->expects($this->exactly(0))->method('sendNotification');
        $this->client->expects($this->exactly(0))->method('flush');

        $this->consumer->process($notification);
    }

    public function testItLogsErrorWhenItWasNotPossibleToSendMessage()
    {
        $messageSentReport = $this->getMockBuilder(\Minishlink\WebPush\MessageSentReport::class)
            ->disableOriginalConstructor()
            ->getMock();

        $messageSentReport->method('isSuccess')->willReturn(false);
        $messageSentReport->method('getReason')->willReturn('error message');

        $deviceId = $this->deviceHelper->createDevice('endpointTest');
        $notification = new \MageSuite\PwaNotifications\Model\Data\Notification();
        $notification->setDeviceId($deviceId);
        $notification->setBody('pwa message');

        $this->client->method('flush')->willReturn($this->createGenerator([$messageSentReport]));
        $this->logger->expects($this->once())->method('error')->with('Failed to push PWA notification: error message');

        $this->consumer->process($notification);
    }

    protected function createGenerator($returnedValues)
    {
        foreach ($returnedValues as $returnValue) {
            yield $returnValue;
        }
    }
}
