<?php

namespace MageSuite\PwaNotifications\Model\Notification;

class SendByDevice
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
     * @param string $deviceId
     * @param $notification
     */
    public function execute($deviceId, $notification, $requiredPermissions = [])
    {
        if (empty($deviceId)) {
            return;
        }

        if (!$this->devicesHavePermissions->execute([$deviceId], $requiredPermissions)) {
            return;
        }

        $this->publishToQueue->execute($deviceId, $notification);
    }
}
