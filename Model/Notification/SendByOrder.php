<?php

namespace MageSuite\PwaNotifications\Model\Notification;

class SendByOrder implements \MageSuite\PwaNotifications\Api\SendNotificationInterface
{
    /**
     * @var \MageSuite\PwaNotifications\Model\Notification\PublishToQueue
     */
    protected $publishToQueue;

    /**
     * @var \MageSuite\PwaNotifications\Model\OrderToDeviceRepository
     */
    protected $orderToDeviceRepository;

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
        \MageSuite\PwaNotifications\Model\OrderToDeviceRepository $orderToDeviceRepository,
        \MageSuite\PwaNotifications\Model\EmailToDeviceRepository $emailToDeviceRepository,
        \MageSuite\PwaNotifications\Model\CustomerToDeviceRepository $customerToDeviceRepository,
        \MageSuite\PwaNotifications\Model\Permission\DevicesHavePermissions $devicesHavePermissions
    ) {
        $this->publishToQueue = $publishToQueue;
        $this->orderToDeviceRepository = $orderToDeviceRepository;
        $this->emailToDeviceRepository = $emailToDeviceRepository;
        $this->customerToDeviceRepository = $customerToDeviceRepository;
        $this->devicesHavePermissions = $devicesHavePermissions;
    }

    /**
     * @param $order
     * @param \MageSuite\PwaNotifications\Api\Data\NotificationInterface $notification
     * @param array $requiredPermissions
     * @return string
     */
    public function execute($order, \MageSuite\PwaNotifications\Api\Data\NotificationInterface $notification, $requiredPermissions = [])
    {
        $orderId = $order->getId();
        $email = $order->getCustomerEmail();
        $customerId = $order->getCustomerId();

        $deviceIds = $this->orderToDeviceRepository->getDevicesByOrderId($orderId);
        $deviceIds = array_merge($deviceIds, $this->emailToDeviceRepository->getDevicesByEmail($email));

        if (is_numeric($customerId) && $customerId > 0) {
            $deviceIds = array_merge($deviceIds, $this->customerToDeviceRepository->getDevicesByCustomerId($customerId));
        }

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
