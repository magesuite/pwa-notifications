<?php

namespace MageSuite\PwaNotifications\Model\Data;

class EncryptionKeys implements \MageSuite\PwaNotifications\Api\Data\EncryptionKeysInterface
{
    protected $auth;
    protected $p256dh;

    /**
     * @inheritDoc
     */
    public function getP256dh(): string
    {
        return $this->p256dh;
    }

    /**
     * @inheritDoc
     */
    public function setP256dh(string $p256dh)
    {
        $this->p256dh = $p256dh;

        return $this->p256dh;
    }

    /**
     * @inheritDoc
     */
    public function getAuth(): string
    {
        return $this->auth;
    }

    /**
     * @inheritDoc
     */
    public function setAuth(string $auth)
    {
        $this->auth = $auth;

        return $this->auth;
    }
}
