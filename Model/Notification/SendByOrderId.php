<?php

namespace MageSuite\PwaNotifications\Model\Notification;

class SendByOrderId
{
    /**
     * @var PublishToQueue
     */
    protected $publishToQueue;

    /**
     * @var \MageSuite\PwaNotifications\Model\OrderToDeviceRepository
     */
    protected $orderToDeviceRepository;

    public function __construct(
        PublishToQueue $publishToQueue,
        \MageSuite\PwaNotifications\Model\OrderToDeviceRepository $orderToDeviceRepository
    )
    {
        $this->publishToQueue = $publishToQueue;
        $this->orderToDeviceRepository = $orderToDeviceRepository;
    }

    public function execute($orderId, $message) {
        $deviceIds = $this->orderToDeviceRepository->getDevicesByOrderId($orderId);

        if(empty($deviceIds)) {
            return;
        }

        foreach($deviceIds as $deviceId) {
            $this->publishToQueue->execute($deviceId, $message);
        }
    }
}
