<?php

namespace MageSuite\PwaNotifications\Setup\Patch\Data;

class GenerateServerKeys implements \Magento\Framework\Setup\Patch\DataPatchInterface {
    /**
     * @var \Magento\Framework\App\Config\Storage\WriterInterface
     */
    protected $configWriter;

    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    protected $encryptor;

    public function __construct(
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor
    )
    {
        $this->configWriter = $configWriter;
        $this->encryptor = $encryptor;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $keys = \Minishlink\WebPush\VAPID::createVapidKeys();

        $encryptedPublicKey = $this->encryptor->encrypt($keys['publicKey']);
        $encryptedPrivateKey = $this->encryptor->encrypt($keys['privateKey']);

        $this->configWriter->save(\MageSuite\PwaNotifications\Helper\Configuration::XML_PATH_SERVER_PUBLIC_KEY, $encryptedPublicKey);
        $this->configWriter->save(\MageSuite\PwaNotifications\Helper\Configuration::XML_PATH_SERVER_PRIVATE_KEY, $encryptedPrivateKey);
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getAliases()
    {
        return [];
    }
}

