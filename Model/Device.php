<?php

namespace MageSuite\PwaNotifications\Model;

class Device extends \Magento\Framework\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init(\MageSuite\PwaNotifications\Model\ResourceModel\Device::class);
    }

    public function getP256dh() {
        return $this->getData('p256dh');
    }

    public function setP256dh($p256dh) {
        $this->setData('p256dh', $p256dh);

        return $this;
    }
}
