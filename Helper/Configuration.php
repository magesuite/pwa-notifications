<?php

namespace MageSuite\PwaNotifications\Helper;

class Configuration extends \Magento\Framework\App\Helper\AbstractHelper implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    const XML_PATH_SERVER_PUBLIC_KEY = 'pwa/notifications/server_public_key';
    const XML_PATH_SERVER_PRIVATE_KEY = 'pwa/notifications/server_private_key';
    const XML_PATH_ORDER_NOTIFY_ABOUT_ORDER_SHIPMENT = 'pwa/order/notify_about_order_shipment';

    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    protected $encryptor;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct($context);

        $this->encryptor = $encryptor;
        $this->scopeConfig = $scopeConfig;
    }

    public function getServerPublicKey()
    {
        return $this->getDecryptedValue(self::XML_PATH_SERVER_PUBLIC_KEY);
    }

    public function getServerPrivateKey()
    {
        return $this->getDecryptedValue(self::XML_PATH_SERVER_PRIVATE_KEY);
    }

    protected function getDecryptedValue($xmlPath)
    {
        return $this->encryptor->decrypt($this->scopeConfig->getValue($xmlPath));
    }

    public function shouldNotifyAboutOrderShipment()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_ORDER_NOTIFY_ABOUT_ORDER_SHIPMENT);
    }
}
