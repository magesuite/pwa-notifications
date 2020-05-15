<?php

namespace MageSuite\PwaNotifications\Model\Notification;

class SendByEmail
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
     * @param string $email
     * @param $message
     */
    public function execute($email, $notification, $requiredPermissions = [])
    {
        $deviceIds = $this->emailToDeviceRepository->getDevicesByEmail($email);

        if (empty($deviceIds)) {
            return;
        }

        if (!$this->devicesHavePermissions->execute($deviceIds, $requiredPermissions)) {
            return;
        }

        foreach ($deviceIds as $deviceId) {
            $this->publishToQueue->execute($deviceId, $notification);
        }
    }
}
