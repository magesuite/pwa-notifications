<?php

namespace MageSuite\PwaNotifications\Model\Data;

class Notification implements \MageSuite\PwaNotifications\Api\Data\NotificationInterface
{
    /**
     * @var int
     */
    protected $deviceId;

    /**
     * @var string
     */
    protected $message;

    /**
     * @inheritDoc
     */
    public function getDeviceId()
    {
        return $this->deviceId;
    }

    /**
     * @inheritDoc
     */
    public function setDeviceId($deviceId)
    {
        $this->deviceId = $deviceId;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @inheritDoc
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    public function __toString()
    {
        return json_encode([
            'deviceId' => $this->getDeviceId(),
            'message' => $this->getMessage(),
        ]);
    }
}
