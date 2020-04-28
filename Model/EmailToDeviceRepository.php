<?php

namespace MageSuite\PwaNotifications\Model;

class EmailToDeviceRepository
{
    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection;

    public function __construct(\Magento\Framework\App\ResourceConnection $resourceConnection)
    {
        $this->connection = $resourceConnection->getConnection();
    }

    public function save($email, $deviceId) {
        $table = $this->connection->getTableName('pwa_email_device');

        return $this->connection->insertOnDuplicate($table, [
            'email' => $email,
            'device_id' => $deviceId,
        ]);
    }

    public function getEmailsByDeviceId($deviceId) {
        $table = $this->connection->getTableName('pwa_email_device');

        $select = $this->connection->select();
        $select->from($table);
        $select->columns(['email']);
        $select->where('device_id = ?', $deviceId);

        return $this->connection->fetchCol($select);
    }
}
