<?php

namespace MageSuite\PwaNotifications\Api;

interface DeviceInformationManagementInterface
{
    /**
     * @param string $endpoint
     * @param Data\EncryptionKeysInterface $keys
     * @return string
     */
    public function save(string $endpoint, \MageSuite\PwaNotifications\Api\Data\EncryptionKeysInterface $keys);
}
