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

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    public function __construct(
        \MageSuite\PwaNotifications\Model\DeviceFactory $deviceFactory,
        \Magento\Framework\Event\ManagerInterface $eventManagerInterface,
        \Magento\Framework\Session\SessionManagerInterface $sessionManager,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->deviceFactory = $deviceFactory;
        $this->eventManagerInterface = $eventManagerInterface;
        $this->sessionManager = $sessionManager;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function get(string $endpoint)
    {
        $device = $this->getDeviceByEndpoint($endpoint);

        if (!$device->getId()) {
            return '';
        }

        return $device->getIdentifier();
    }

    public function save(string $endpoint, \MageSuite\PwaNotifications\Api\Data\EncryptionKeysInterface $keys, string $oldEndpoint = '', array $permissions = []) //phpcs:ignore
    {
        $deviceIdFromSession = $this->sessionManager->getPwaDeviceId();
        $identifier = hash('sha256', $endpoint . $keys->getP256dh() . $keys->getAuth());

        if (empty($oldEndpoint)) {
            $device = $this->getDeviceByEndpoint($endpoint);

            if (!$device->getId() && $deviceIdFromSession > 0) {
                $device->load($deviceIdFromSession, 'device_id');
            }
        } else {
            $device = $this->getDeviceByEndpoint($oldEndpoint);

            if (!$device->getId() && $deviceIdFromSession > 0) {
                $device->load($deviceIdFromSession, 'device_id');
            }
        }

        if (!$device->getId()) {
            $device->setEndpoint($endpoint);
            $device->setP256dh($keys->getP256dh());
            $device->setAuth($keys->getAuth());
            $device->setPermissions($permissions);
            $device->setIdentifier($identifier);

            $device->save();
        } elseif (!empty($oldEndpoint) && $device->getId()) {
            $this->logger->info(sprintf('Replacing endpoint and keys for device with id %s', $device->getId()));

            $device->setEndpoint($endpoint);
            $device->setP256dh($keys->getP256dh());
            $device->setAuth($keys->getAuth());
            $device->setIdentifier($identifier);

            $device->save();
        } elseif (!empty($oldEndpoint)) {
            $this->logger->error(sprintf('Tried to replace device endpoint, but device was not found based on old endpoint "%s".', $oldEndpoint));
        }

        $this->eventManagerInterface->dispatch('pwa_device_saved', ['device' => $device]);

        $this->sessionManager->setPwaDeviceId($device->getId());

        return $device->getEndpoint();
    }

    public function getDeviceByIdentifier($identifier)
    {
        /** @var Device $device */
        $device = $this->deviceFactory->create();
        $device->load($identifier, 'identifier');

        return $device;
    }

    public function getDeviceByEndpoint($endpoint)
    {
        /** @var Device $device */
        $device = $this->deviceFactory->create();
        $device->load($endpoint, 'endpoint');

        return $device;
    }
}
