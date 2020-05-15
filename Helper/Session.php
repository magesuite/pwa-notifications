<?php

namespace MageSuite\PwaNotifications\Helper;

class Session extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    protected $sessionManager;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Session\SessionManagerInterface $sessionManager
    ) {
        parent::__construct($context);

        $this->sessionManager = $sessionManager;
    }

    public function getDeviceId()
    {
        return $this->sessionManager->getPwaDeviceId();
    }

    public function setDeviceId($deviceId)
    {
        $this->sessionManager->setPwaDeviceId($deviceId);
    }
}
