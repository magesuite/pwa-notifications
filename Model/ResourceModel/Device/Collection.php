<?php

namespace MageSuite\PwaNotifications\Model\ResourceModel\Device;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'device_id';

    protected function _construct()
    {
        $this->_init(\MageSuite\PwaNotifications\Model\Device::class, \MageSuite\PwaNotifications\Model\ResourceModel\Device::class);
    }
}
