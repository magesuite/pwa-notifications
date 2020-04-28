<?php

namespace MageSuite\PwaNotifications\Observer\Order\Shipment;

class NotifyAboutOrderShipment implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \MageSuite\PwaNotifications\Model\Notification\SendByOrderId
     */
    protected $sendByOrderId;

    public function __construct(\MageSuite\PwaNotifications\Model\Notification\SendByOrderId $sendByOrderId)
    {
        $this->sendByOrderId = $sendByOrderId;
    }

    /**
     * @inheritDoc
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $shipment = $observer->getEvent()->getShipment();

        if(!$shipment) {
            return;
        }

        $order = $shipment->getOrder();

        if(!$order) {
            return;
        }

        $orderId = $order->getId();

        $this->sendByOrderId->execute($orderId, 'Your order was shipped');
    }
}
