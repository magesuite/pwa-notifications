<?php

namespace MageSuite\PwaNotifications\Api;

interface DeviceInformationManagementInterface
{
    /**
     * @param string $endpoint
     * @param Data\EncryptionKeysInterface $keys
     * @param string[] $permissions
     * @param string $oldEndpoint
     * @return string
     */
    public function save( //phpcs:ignore
        string $endpoint,
        \MageSuite\PwaNotifications\Api\Data\EncryptionKeysInterface $keys,
        string $oldEndpoint = '',
        array $permissions = []
    );

    /**
     * @param string $endpoint
     * @return string
     */
    public function get(string $endpoint);
}
