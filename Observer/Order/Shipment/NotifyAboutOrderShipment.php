<?php

namespace MageSuite\PwaNotifications\Observer\Order\Shipment;

class NotifyAboutOrderShipment implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \MageSuite\PwaNotifications\Helper\Configuration
     */
    protected $configuration;

    /**
     * @var \MageSuite\PwaNotifications\Model\Notification\SendByOrder
     */
    protected $sendByOrder;

    /**
     * @var \MageSuite\PwaNotifications\Api\Data\NotificationInterfaceFactory
     */
    protected $notificationFactory;

    public function __construct(
        \MageSuite\PwaNotifications\Helper\Configuration $configuration,
        \MageSuite\PwaNotifications\Api\Data\NotificationInterfaceFactory $notificationFactory,
        \MageSuite\PwaNotifications\Model\Notification\SendByOrder $sendByOrder
    ) {
        $this->configuration = $configuration;
        $this->sendByOrder = $sendByOrder;
        $this->notificationFactory = $notificationFactory;
    }

    /**
     * @inheritDoc
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->configuration->shouldNotifyAboutOrderShipment()) {
            return;
        }

        $shipment = $observer->getEvent()->getShipment();

        if (!$shipment) {
            return;
        }

        /** @var \Magento\Sales\Model\Order $order */
        $order = $shipment->getOrder();

        if (!$order) {
            return;
        }

        $notification = $this->notificationFactory->create();
        $notification->setTitle('Order status');
        $notification->setBody(sprintf('Your order %s was shipped', $order->getIncrementId()));

        $this->sendByOrder->execute($order, $notification, ['order_status_notification']);
    }
}
