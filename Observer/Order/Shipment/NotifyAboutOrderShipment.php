<?php

namespace MageSuite\PwaNotifications\Observer\Order\Shipment;

class NotifyAboutOrderShipment implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \MageSuite\PwaNotifications\Model\Notification\SendByOrder
     */
    protected $sendByOrder;

    /**
     * @var \MageSuite\PwaNotifications\Api\Data\NotificationInterfaceFactory
     */
    protected $notificationFactory;

    /**
     * @var \Magento\Store\Model\App\Emulation
     */
    protected $emulation;

    public function __construct(
        \MageSuite\PwaNotifications\Api\Data\NotificationInterfaceFactory $notificationFactory,
        \MageSuite\PwaNotifications\Model\Notification\SendByOrder $sendByOrder,
        \Magento\Store\Model\App\Emulation $emulation
    ) {
        $this->sendByOrder = $sendByOrder;
        $this->notificationFactory = $notificationFactory;
        $this->emulation = $emulation;
    }

    /**
     * @inheritDoc
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $shipment = $observer->getEvent()->getShipment();

        if (!$shipment) {
            return;
        }

        /** @var \Magento\Sales\Model\Order $order */
        $order = $shipment->getOrder();
        $storeId = $order->getStoreId();

        if (!$order) {
            return;
        }

        $this->emulation->startEnvironmentEmulation($storeId);

        $notification = $this->notificationFactory->create();
        $notification->setTitle(__('Order status'));
        $notification->setBody(__('Your order %1 was shipped', $order->getIncrementId()));

        $this->sendByOrder->execute($order, $notification, ['order_status_notification']);

        $this->emulation->stopEnvironmentEmulation();
    }
}
