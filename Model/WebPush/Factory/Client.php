<?php

namespace MageSuite\PwaNotifications\Model\WebPush\Factory;

class Client
{
    /**
     * @var \MageSuite\PwaNotifications\Helper\Configuration
     */
    protected $configuration;

    public function __construct(\MageSuite\PwaNotifications\Helper\Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @return \Minishlink\WebPush\WebPush
     * @throws \ErrorException
     */
    public function create() {
        $auth = [
            'VAPID' => [
                'subject' => 'https://magesuite.me',
                'publicKey' => $this->configuration->getServerPublicKey(),
                'privateKey' => $this->configuration->getServerPrivateKey(),
            ],
        ];

        return new \Minishlink\WebPush\WebPush($auth);
    }
}
