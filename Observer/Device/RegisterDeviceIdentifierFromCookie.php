<?php

namespace MageSuite\PwaNotifications\Observer\Device;

class RegisterDeviceIdentifierFromCookie implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \MageSuite\PwaNotifications\Helper\Session
     */
    protected $session;

    /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface
     */
    protected $cookieManager;

    /**
     * @var \MageSuite\PwaNotifications\Model\DeviceFactory
     */
    protected $deviceFactory;

    public function __construct(
        \MageSuite\PwaNotifications\Helper\Session $session,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \MageSuite\PwaNotifications\Model\DeviceFactory $deviceFactory
    ) {
        $this->session = $session;
        $this->cookieManager = $cookieManager;
        $this->deviceFactory = $deviceFactory;
    }

    /**
     * @inheritDoc
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $identifier = $this->cookieManager->getCookie('pwa_device_identifier', null);

        if ($identifier == null || empty($identifier)) {
            return;
        }

        $device = $this->deviceFactory->create();
        $device->load($identifier, 'identifier');

        if (!$device->getId()) {
            $this->cookieManager->deleteCookie('pwa_device_identifier');
        }

        $this->session->setDeviceId($device->getId());
    }
}
