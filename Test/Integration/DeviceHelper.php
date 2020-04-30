<?php

namespace MageSuite\PwaNotifications\Test\Integration;

class DeviceHelper
{
    /** @var \Magento\TestFramework\ObjectManager */
    protected $objectManager;

    /**
     * @var \MageSuite\PwaNotifications\Model\DeviceInformationManagement
     */
    protected $deviceInformationManagement;

    public function __construct()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->deviceInformationManagement = $this->objectManager->create(\MageSuite\PwaNotifications\Model\DeviceInformationManagement::class);
    }

    public function createDevice($endpoint)
    {
        $encryptionKeys = $this->objectManager->create(\MageSuite\PwaNotifications\Api\Data\EncryptionKeysInterface::class);
        $encryptionKeys->setP256dh('p256dhTest');
        $encryptionKeys->setAuth('authTest');

        $deviceIdentifier = $this->deviceInformationManagement->save($endpoint, $encryptionKeys);
        $deviceId = $this->deviceInformationManagement->getDeviceByIdentifier($deviceIdentifier)->getId();

        return $deviceId;
    }
}
