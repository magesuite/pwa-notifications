<?php

namespace MageSuite\PwaNotifications\Model\Notification;

class SendByEmail implements \MageSuite\PwaNotifications\Api\SendNotificationInterface
{
    /**
     * @var \MageSuite\PwaNotifications\Model\Notification\PublishToQueue
     */
    protected $publishToQueue;

    /**
     * @var \MageSuite\PwaNotifications\Model\EmailToDeviceRepository
     */
    protected $emailToDeviceRepository;

    /**
     * @var \MageSuite\PwaNotifications\Model\Permission\DevicesHavePermissions
     */
    protected $devicesHavePermissions;

    public function __construct(
        \MageSuite\PwaNotifications\Model\Notification\PublishToQueue $publishToQueue,
        \MageSuite\PwaNotifications\Model\EmailToDeviceRepository $emailToDeviceRepository,
        \MageSuite\PwaNotifications\Model\Permission\DevicesHavePermissions $devicesHavePermissions
    ) {
        $this->publishToQueue = $publishToQueue;
        $this->emailToDeviceRepository = $emailToDeviceRepository;
        $this->devicesHavePermissions = $devicesHavePermissions;
    }

    /**
     * @param $email
     * @param \MageSuite\PwaNotifications\Api\Data\NotificationInterface $notification
     * @param array $requiredPermissions
     * @return string
     */
    public function execute($email, \MageSuite\PwaNotifications\Api\Data\NotificationInterface $notification, $requiredPermissions = [])
    {
        $deviceIds = $this->emailToDeviceRepository->getDevicesByEmail($email);

        if (empty($deviceIds)) {
            return \MageSuite\PwaNotifications\Api\SendNotificationInterface::STATUS_MISSING_DEVICE_ID;
        }

        if (!$this->devicesHavePermissions->execute($deviceIds, $requiredPermissions)) {
            return \MageSuite\PwaNotifications\Api\SendNotificationInterface::STATUS_INSUFFICIENT_PERMISSIONS;
        }

        foreach ($deviceIds as $deviceId) {
            $this->publishToQueue->execute($deviceId, $notification);
        }

        return \MageSuite\PwaNotifications\Api\SendNotificationInterface::STATUS_SENT;
    }
}
