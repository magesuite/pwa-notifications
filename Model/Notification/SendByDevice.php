<?php

namespace MageSuite\PwaNotifications\Model\Notification;

class SendByDevice implements \MageSuite\PwaNotifications\Api\SendNotificationInterface
{
    /**
     * @var \MageSuite\PwaNotifications\Model\Notification\PublishToQueue
     */
    protected $publishToQueue;

    /**
     * @var \MageSuite\PwaNotifications\Model\Permission\DevicesHavePermissions
     */
    protected $devicesHavePermissions;

    public function __construct(
        \MageSuite\PwaNotifications\Model\Notification\PublishToQueue $publishToQueue,
        \MageSuite\PwaNotifications\Model\Permission\DevicesHavePermissions $devicesHavePermissions
    ) {
        $this->publishToQueue = $publishToQueue;
        $this->devicesHavePermissions = $devicesHavePermissions;
    }

    /**
     * @param $deviceId
     * @param \MageSuite\PwaNotifications\Api\Data\NotificationInterface $notification
     * @param array $requiredPermissions
     * @return string
     */
    public function execute($deviceId, \MageSuite\PwaNotifications\Api\Data\NotificationInterface $notification, $requiredPermissions = [])
    {
        if (empty($deviceId)) {
            return \MageSuite\PwaNotifications\Api\SendNotificationInterface::STATUS_MISSING_DEVICE_ID;
        }

        if (!$this->devicesHavePermissions->execute([$deviceId], $requiredPermissions)) {
            return \MageSuite\PwaNotifications\Api\SendNotificationInterface::STATUS_INSUFFICIENT_PERMISSIONS;
        }

        $this->publishToQueue->execute($deviceId, $notification);
        return \MageSuite\PwaNotifications\Api\SendNotificationInterface::STATUS_SENT;
    }
}
