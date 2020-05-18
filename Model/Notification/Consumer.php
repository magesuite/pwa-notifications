<?php

namespace MageSuite\PwaNotifications\Model\Notification;

class Consumer
{
    /**
     * @var \MageSuite\PwaNotifications\Model\DeviceFactory
     */
    protected $deviceFactory;

    /**
     * @var \MageSuite\PwaNotifications\Model\WebPush\Factory\Subscription
     */
    protected $subscriptionFactory;

    /**
     * @var \MageSuite\PwaNotifications\Model\WebPush\Factory\Client
     */
    protected $clientFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    public function __construct(
        \MageSuite\PwaNotifications\Model\DeviceFactory $deviceFactory,
        \MageSuite\PwaNotifications\Model\WebPush\Factory\Subscription $subscriptionFactory,
        \MageSuite\PwaNotifications\Model\WebPush\Factory\Client $clientFactory,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->deviceFactory = $deviceFactory;
        $this->subscriptionFactory = $subscriptionFactory;
        $this->clientFactory = $clientFactory;
        $this->logger = $logger;
    }

    public function process(\MageSuite\PwaNotifications\Api\Data\NotificationInterface $notification)
    {
        $device = $this->deviceFactory->create();
        $device->load($notification->getDeviceId());

        if (!$device->getId()) {
            $this->logger->error(sprintf('Device with id %s does not exist.', $notification->getDeviceId()));
            return;
        }

        $webPush = $this->clientFactory->create();
        $subscription = $this->subscriptionFactory->create($device);

        $webPush->sendNotification($subscription, (string)$notification);

        /** @var \Minishlink\WebPush\MessageSentReport $report */
        foreach ($webPush->flush() as $report) {
            if (!$report->isSuccess()) {
                $this->logger->error('Failed to push PWA notification: ' . $report->getReason());
            }
        }
    }
}
