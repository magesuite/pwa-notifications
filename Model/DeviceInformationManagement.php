<?php

namespace MageSuite\PwaNotifications\Model;

class DeviceInformationManagement implements \MageSuite\PwaNotifications\Api\DeviceInformationManagementInterface
{
    /**
     * @var \MageSuite\PwaNotifications\Model\DeviceFactory
     */
    protected $deviceFactory;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManagerInterface;

    /**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    protected $sessionManager;

    public function __construct(
        \MageSuite\PwaNotifications\Model\DeviceFactory $deviceFactory,
        \Magento\Framework\Event\ManagerInterface $eventManagerInterface,
        \Magento\Framework\Session\SessionManagerInterface $sessionManager
    ) {
        $this->deviceFactory = $deviceFactory;
        $this->eventManagerInterface = $eventManagerInterface;
        $this->sessionManager = $sessionManager;
    }

    public function save(string $endpoint, \MageSuite\PwaNotifications\Api\Data\EncryptionKeysInterface $keys)
    {
        $identifier = hash('sha256', $endpoint . $keys->getP256dh() . $keys->getAuth());

        /** @var Device $device */
        $device = $this->deviceFactory->create();
        $device->load($identifier, 'identifier');

        if (!$device->getId()) {
            $device->setEndpoint($endpoint);
            $device->setP256dh($keys->getP256dh());
            $device->setAuth($keys->getAuth());
            $device->setIdentifier($identifier);

            $device->save();
        }

        $this->eventManagerInterface->dispatch('pwa_device_saved', ['device' => $device]);

        $this->sessionManager->setPwaDeviceId($device->getId());

        return $device->getIdentifier();
    }

    public function getDeviceByIdentifier($identifier)
    {
        /** @var Device $device */
        $device = $this->deviceFactory->create();
        $device->load($identifier, 'identifier');

        return $device;
    }
}
