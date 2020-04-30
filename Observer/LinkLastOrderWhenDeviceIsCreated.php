<?php

namespace MageSuite\PwaNotifications\Observer;

class LinkLastOrderWhenDeviceIsCreated implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \MageSuite\PwaNotifications\Model\Order\LinkOrderWithDevice
     */
    protected $linkOrderWithDevice;

    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \MageSuite\PwaNotifications\Model\Order\LinkOrderWithDevice $linkOrderWithDevice
    )
    {
        $this->checkoutSession = $checkoutSession;
        $this->linkOrderWithDevice = $linkOrderWithDevice;
    }

    /**
     * @inheritDoc
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \MageSuite\PwaNotifications\Model\Device $device */
        $device = $observer->getData('device');

        $order = $this->checkoutSession->getLastRealOrder();

        if($order == null) {
            return;
        }

        $orderId = $order->getId();

        if(!is_numeric($orderId)) {
            return;
        }

        $this->linkOrderWithDevice->execute($order, $device->getId());
    }
}
