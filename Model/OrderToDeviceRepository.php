<?php

namespace MageSuite\PwaNotifications\Model;

class OrderToDeviceRepository
{
    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection;

    public function __construct(\Magento\Framework\App\ResourceConnection $resourceConnection)
    {
        $this->connection = $resourceConnection->getConnection();
    }

    public function save($orderId, $deviceId) {
        $table = $this->connection->getTableName('pwa_order_device');

        return $this->connection->insertOnDuplicate($table, [
            'order_id' => $orderId,
            'device_id' => $deviceId,
        ]);
    }
}
