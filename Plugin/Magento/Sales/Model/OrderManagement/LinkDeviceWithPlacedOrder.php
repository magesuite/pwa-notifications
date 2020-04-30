<?php

namespace MageSuite\PwaNotifications\Plugin\Magento\Sales\Model\OrderManagement;

class LinkDeviceWithPlacedOrder
{
    /**
     * @var \MageSuite\PwaNotifications\Helper\Session
     */
    protected $session;

    /**
     * @var \MageSuite\PwaNotifications\Model\Order\LinkOrderWithDevice
     */
    protected $linkOrderWithDevice;

    public function __construct(
        \MageSuite\PwaNotifications\Helper\Session $session,
        \MageSuite\PwaNotifications\Model\Order\LinkOrderWithDevice $linkOrderWithDevice
    ) {
        $this->session = $session;
        $this->linkOrderWithDevice = $linkOrderWithDevice;
    }

    public function afterPlace(
        \Magento\Sales\Model\Service\OrderService $subject,
        $result
    ) {
        if ($this->session->getDeviceId() == null) {
            return $result;
        }

        $deviceId = $this->session->getDeviceId();

        $this->linkOrderWithDevice->execute($result, $deviceId);

        return $result;
    }
}
