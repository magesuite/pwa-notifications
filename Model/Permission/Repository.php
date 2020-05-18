<?php

namespace MageSuite\PwaNotifications\Model\Permission;

class Repository
{
    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection;

    /**
     * @var array
     */
    protected $permissionsIds;

    public function __construct(\Magento\Framework\App\ResourceConnection $resourceConnection)
    {
        $this->connection = $resourceConnection->getConnection();
    }

    public function getPermissionsIds()
    {
        if ($this->permissionsIds == null) {
            $table = $this->connection->getTableName('pwa_permission');

            $select = $this->connection->select();
            $select->from($table, ['identifier', 'permission_id']);

            $this->permissionsIds = $this->connection->fetchAssoc($select);
        }

        return $this->permissionsIds;
    }

    public function getDevicesPermissions($deviceIds)
    {
        $table = $this->connection->getTableName('pwa_device_permission');

        $select = $this->connection->select();
        $select->from($table, ['pwa_permission.identifier']);
        $select->joinLeft(
            ['pwa_permission' => $this->connection->getTableName('pwa_permission')],
            'pwa_device_permission.permission_id = pwa_permission.permission_id',
            ['pwa_permission.identifier']
        );
        $select->group('pwa_permission.identifier');
        $select->where('device_id IN(?)', $deviceIds);

        return $this->connection->fetchCol($select);
    }

    public function removePermissions($deviceIds, $permissions)
    {
        $table = $this->connection->getTableName('pwa_device_permission');

        $permissionsIds = $this->mapPermissionsToIds($permissions);

        foreach ($deviceIds as $deviceId) {
            $this->connection->delete(
                $table,
                [
                    'device_id = ?' => $deviceId,
                    'permission_id IN(?)' => $permissionsIds
                ]
            );
        }
    }

    public function addPermissions($deviceIds, $permissions)
    {
        $table = $this->connection->getTableName('pwa_device_permission');

        $permissionsIds = $this->mapPermissionsToIds($permissions);

        $data = [];

        foreach ($deviceIds as $deviceId) {
            foreach ($permissionsIds as $permissionId) {
                $data[] = [
                    'device_id' => $deviceId,
                    'permission_id' => $permissionId
                ];
            }
        }

        return $this->connection->insertOnDuplicate($table, $data);
    }

    protected function mapPermissionsToIds($permissions)
    {
        $permissionsIds = $this->getPermissionsIds();

        $ids = [];

        foreach ($permissions as $permission) {
            if (!isset($permissionsIds[$permission])) {
                continue;
            }

            $ids[] = $permissionsIds[$permission]['permission_id'];
        }

        return $ids;
    }
}
