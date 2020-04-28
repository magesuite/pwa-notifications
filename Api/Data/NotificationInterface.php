<?php

namespace MageSuite\PwaNotifications\Api\Data;

interface NotificationInterface
{
    /**
     * @return int
     */
    public function getDeviceId();

    /**
     * @param int $deviceId
     * @return self
     */
    public function setDeviceId($deviceId);

    /**
     * @return string
     */
    public function getMessage();

    /**
     * @param string $message
     * @return self
     */
    public function setMessage($message);
}
