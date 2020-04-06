<?php

namespace MageSuite\PwaNotifications\Model\ResourceModel;

class Device extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('pwa_device', 'device_id');
    }
}
