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

    public function __construct(
        \MageSuite\PwaNotifications\Model\DeviceFactory $deviceFactory,
        \Magento\Framework\Event\ManagerInterface $eventManagerInterface
    )
    {
        $this->deviceFactory = $deviceFactory;
        $this->eventManagerInterface = $eventManagerInterface;
    }

    public function save(string $endpoint, \MageSuite\PwaNotifications\Api\Data\EncryptionKeysInterface $keys)
    {
        /** @var Device $device */
        $device = $this->deviceFactory->create();

        $device->setEndpoint($endpoint);
        $device->setP256dh($keys->getP256dh());
        $device->setAuth($keys->getAuth());

        $device->save();

        $this->eventManagerInterface->dispatch('pwa_device_saved', ['device' => $device]);

        return $device->getId();
    }
}
