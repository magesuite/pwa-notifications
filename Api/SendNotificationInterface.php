<?php

namespace MageSuite\PwaNotifications\Api;

interface SendNotificationInterface
{
    const STATUS_SENT = 'sent';
    const STATUS_MISSING_DEVICE_ID = 'missing_device_id';
    const STATUS_INSUFFICIENT_PERMISSIONS = 'insufficient_permissions';

    /**
     * @param $identifier
     * @param Data\NotificationInterface $notification
     * @param array $requiredPermissions
     * @return mixed
     */
    public function execute($identifier, \MageSuite\PwaNotifications\Api\Data\NotificationInterface $notification, $requiredPermissions = []);
}
