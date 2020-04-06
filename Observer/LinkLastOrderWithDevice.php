<?php

namespace MageSuite\PwaNotifications\Observer;

class LinkLastOrderWithDevice implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \MageSuite\PwaNotifications\Model\OrderToDeviceRepository
     */
    protected $orderToDeviceRepository;

    /**
     * @var \MageSuite\PwaNotifications\Model\EmailToDeviceRepository
     */
    protected $emailToDeviceRepository;

    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \MageSuite\PwaNotifications\Model\OrderToDeviceRepository $orderToDeviceRepository,
        \MageSuite\PwaNotifications\Model\EmailToDeviceRepository $emailToDeviceRepository
    )
    {
        $this->checkoutSession = $checkoutSession;
        $this->orderToDeviceRepository = $orderToDeviceRepository;
        $this->emailToDeviceRepository = $emailToDeviceRepository;
    }

    /**
     * @inheritDoc
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \MageSuite\PwaNotifications\Model\Device $device */
        $device = $observer->getData('device');
        $deviceId = $device->getId();

        $order = $this->checkoutSession->getLastRealOrder();

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
    }
}
