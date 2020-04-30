<?php

namespace MageSuite\PwaNotifications\Model\Order;

class LinkOrderWithDevice
{
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
        \MageSuite\PwaNotifications\Model\OrderToDeviceRepository $orderToDeviceRepository,
        \MageSuite\PwaNotifications\Model\EmailToDeviceRepository $emailToDeviceRepository,
        \MageSuite\PwaNotifications\Model\CustomerToDeviceRepository $customerToDeviceRepository
    )
    {
        $this->orderToDeviceRepository = $orderToDeviceRepository;
        $this->emailToDeviceRepository = $emailToDeviceRepository;
        $this->customerToDeviceRepository = $customerToDeviceRepository;
    }

    /**
     * @inheritDoc
     */
    public function execute($order, $deviceId)
    {
        if($order == null) {
            return;
        }

        $orderId = $order->getId();

        if(!is_numeric($orderId)) {
            return;
        }

        $this->orderToDeviceRepository->save($orderId, $deviceId);

        $email = $order->getCustomerEmail();

        if(empty($email)) {
            return;
        }

        $this->emailToDeviceRepository->save($email, $deviceId);

        $customerId = $order->getCustomerId();

        if(!is_numeric($customerId) || $customerId <= 0) {
            return;
        }

        $this->customerToDeviceRepository->save($customerId, $deviceId);
    }
}
