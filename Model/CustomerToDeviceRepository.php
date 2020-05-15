<?php

namespace MageSuite\PwaNotifications\Model;

class CustomerToDeviceRepository
{
    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection;

    public function __construct(\Magento\Framework\App\ResourceConnection $resourceConnection)
    {
        $this->connection = $resourceConnection->getConnection();
    }

    public function save($customerId, $deviceId)
    {
        $table = $this->connection->getTableName('pwa_customer_device');

        return $this->connection->insertOnDuplicate($table, [
            'customer_id' => $customerId,
            'device_id' => $deviceId,
        ]);
    }

    /**
     * @param $deviceId
     * @return int[]
     */
    public function getCustomersByDeviceId($deviceId)
    {
        $table = $this->connection->getTableName('pwa_customer_device');

        $select = $this->connection->select();
        $select->from($table, ['customer_id']);
        $select->where('device_id = ?', $deviceId);

        return $this->connection->fetchCol($select);
    }

    /**
     * @param $orderId
     * @return int[]
     */
    public function getDevicesByCustomerId($customerIds)
    {
        $table = $this->connection->getTableName('pwa_customer_device');

        $select = $this->connection->select();
        $select->from($table, ['device_id']);
        $select->where('customer_id IN(?)', $customerIds);

        return $this->connection->fetchCol($select);
    }

    public function getRelatedDevices($deviceId)
    {
        $customers = $this->getCustomersByDeviceId($deviceId);

        $relatedDevicesIds = [];

        if (empty($customers)) {
            return $relatedDevicesIds;
        }

        $relatedDevicesIds = $this->getDevicesByCustomerId($customers);

        return $relatedDevicesIds;
    }
}
