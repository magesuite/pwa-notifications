<?php

namespace MageSuite\PwaNotifications\Model\Notification;

class SendByOrder
{
    /**
     * @var PublishToQueue
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

    public function __construct(
        PublishToQueue $publishToQueue,
        \MageSuite\PwaNotifications\Model\OrderToDeviceRepository $orderToDeviceRepository,
        \MageSuite\PwaNotifications\Model\EmailToDeviceRepository $emailToDeviceRepository,
        \MageSuite\PwaNotifications\Model\CustomerToDeviceRepository $customerToDeviceRepository
    ) {
        $this->publishToQueue = $publishToQueue;
        $this->orderToDeviceRepository = $orderToDeviceRepository;
        $this->emailToDeviceRepository = $emailToDeviceRepository;
        $this->customerToDeviceRepository = $customerToDeviceRepository;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @param $message
     */
    public function execute($order, $message)
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
            return;
        }

        foreach ($deviceIds as $deviceId) {
            $this->publishToQueue->execute($deviceId, $message);
        }
    }
}
