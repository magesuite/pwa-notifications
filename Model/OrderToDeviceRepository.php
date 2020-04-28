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

    /**
     * @param $deviceId
     * @return int[]
     */
    public function getOrdersByDeviceId($deviceId) {
        $table = $this->connection->getTableName('pwa_order_device');

        $select = $this->connection->select();
        $select->from($table, ['order_id']);
        $select->where('device_id = ?', $deviceId);

        return $this->connection->fetchCol($select);
    }

    /**
     * @param $orderId
     * @return int[]
     */
    public function getDevicesByOrderId($orderId) {
        $table = $this->connection->getTableName('pwa_order_device');

        $select = $this->connection->select();
        $select->from($table, ['device_id']);
        $select->where('order_id = ?', $orderId);

        return $this->connection->fetchCol($select);
    }
}
