<?php

namespace MageSuite\PwaNotifications\Model\Notification;

class SendByCustomer implements \MageSuite\PwaNotifications\Api\SendNotificationInterface
{
    /**
     * @var PublishToQueue
     */
    protected $publishToQueue;

    /**
     * @var \MageSuite\PwaNotifications\Model\EmailToDeviceRepository
     */
    protected $emailToDeviceRepository;

    /**
     * @var \MageSuite\PwaNotifications\Model\CustomerToDeviceRepository
     */
    protected $customerToDeviceRepository;

    /**
     * @var \MageSuite\PwaNotifications\Model\Permission\DevicesHavePermissions
     */
    protected $devicesHavePermissions;

    public function __construct(
        \MageSuite\PwaNotifications\Model\Notification\PublishToQueue $publishToQueue,
        \MageSuite\PwaNotifications\Model\EmailToDeviceRepository $emailToDeviceRepository,
        \MageSuite\PwaNotifications\Model\CustomerToDeviceRepository $customerToDeviceRepository,
        \MageSuite\PwaNotifications\Model\Permission\DevicesHavePermissions $devicesHavePermissions
    ) {
        $this->publishToQueue = $publishToQueue;
        $this->emailToDeviceRepository = $emailToDeviceRepository;
        $this->customerToDeviceRepository = $customerToDeviceRepository;
        $this->devicesHavePermissions = $devicesHavePermissions;
    }

    /**
     * @param $customer
     * @param \MageSuite\PwaNotifications\Api\Data\NotificationInterface $notification
     * @param array $requiredPermissions
     * @return string
     */
    public function execute($customer, \MageSuite\PwaNotifications\Api\Data\NotificationInterface $notification, $requiredPermissions = [])
    {
        $deviceIds = $this->customerToDeviceRepository->getDevicesByCustomerId($customer->getId());
        $deviceIds = array_merge($deviceIds, $this->emailToDeviceRepository->getDevicesByEmail($customer->getEmail()));

        $deviceIds = array_unique($deviceIds);

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
