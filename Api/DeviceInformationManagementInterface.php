<?php

namespace MageSuite\PwaNotifications\Api;

interface DeviceInformationManagementInterface
{
    /**
     * @param string $endpoint
     * @param Data\EncryptionKeysInterface $keys
     * @param string[] $permissions
     * @return string
     */
    public function save(string $endpoint, \MageSuite\PwaNotifications\Api\Data\EncryptionKeysInterface $keys, array $permissions = []);

    /**
     * @param string $endpoint
     * @return string
     */
    public function get(string $endpoint);
}
