<?php

namespace MageSuite\PwaNotifications\Model\Notification;

class PublishToQueue
{
    /**
     * @var \MageSuite\Queue\Service\Publisher
     */
    protected $queuePublisher;

    public function __construct(\MageSuite\Queue\Service\Publisher $queuePublisher)
    {
        $this->queuePublisher = $queuePublisher;
    }

    public function execute($deviceId, \MageSuite\PwaNotifications\Api\Data\NotificationInterface $notification)
    {
        $notification->setDeviceId($deviceId);

        $this->queuePublisher->publish(
            \MageSuite\PwaNotifications\Model\Notification\Queue\Consumer::class,
            (string)$notification
        );
    }
}
