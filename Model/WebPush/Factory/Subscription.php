<?php

namespace MageSuite\PwaNotifications\Model\WebPush\Factory;

class Subscription
{
    public function create($device)
    {
        return \Minishlink\WebPush\Subscription::create([
            'endpoint' => $device->getEndpoint(),
            'publicKey' => $device->getP256dh(),
            'authToken' => $device->getAuth(),
        ]);
    }
}
