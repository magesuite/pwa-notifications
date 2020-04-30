<?php

namespace MageSuite\PwaNotifications\Observer\Order\Shipment;

class NotifyAboutOrderShipment implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \MageSuite\PwaNotifications\Model\Notification\SendByOrder
     */
    protected $sendByOrder;

    public function __construct(\MageSuite\PwaNotifications\Model\Notification\SendByOrder $sendByOrder)
    {
        $this->sendByOrder = $sendByOrder;
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

        /** @var \Magento\Sales\Model\Order $order */
        $order = $shipment->getOrder();

        if(!$order) {
            return;
        }

        $this->sendByOrder->execute($order, sprintf('Your order %s was shipped', $order->getIncrementId()));
    }
}
