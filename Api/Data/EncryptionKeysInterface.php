<?php

namespace MageSuite\PwaNotifications\Api\Data;

interface EncryptionKeysInterface
{
    /**
     * @return string
     */
    public function getP256dh(): string;

    /**
     * @param string $p256dh
     * @return self
     */
    public function setP256dh(string $p256dh);

    /**
     * @return string
     */
    public function getAuth(): string;

    /**
     * @param string $auth
     * @return self
     */
    public function setAuth(string $auth);
}
