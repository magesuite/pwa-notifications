<?php
declare(strict_types=1);

namespace MageSuite\PwaNotifications\Setup;

class Recurring implements \Magento\Framework\Setup\InstallSchemaInterface
{
    protected \MageSuite\PwaNotifications\Model\Permission\Pool $permissionPool;

    public function __construct(\MageSuite\PwaNotifications\Model\Permission\Pool $permissionPool)
    {
        $this->permissionPool = $permissionPool;
    }

    public function install(\Magento\Framework\Setup\SchemaSetupInterface $setup, \Magento\Framework\Setup\ModuleContextInterface $context): void
    {
        $setup->startSetup();
        $connection = $setup->getConnection();
        $tableName = $connection->getTableName('pwa_permission');

        foreach ($this->permissionPool->getPermissions() as $permissionIdentifier => $permission) {
            $connection->insertOnDuplicate($tableName, ['identifier' => $permissionIdentifier]);
        }

        $setup->endSetup();
    }
}
