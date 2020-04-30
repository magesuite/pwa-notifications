<?php

namespace MageSuite\PwaNotifications\Model\Notification;

class PublishToQueue
{
    const TOPIC = 'pwa.notification.send';

    /**
     * @var \Magento\Framework\MessageQueue\Publisher
     */
    protected $queuePublisher;

    public function __construct(\Magento\Framework\MessageQueue\Publisher $queuePublisher)
    {

        $this->queuePublisher = $queuePublisher;
    }

    public function execute($deviceId, $notificationMessage)
    {
        $notification = new \MageSuite\PwaNotifications\Model\Data\Notification();
        $notification->setDeviceId($deviceId);
        $notification->setMessage($notificationMessage);

        $this->queuePublisher->publish(
            self::TOPIC,
            $notification
        );
    }
}
