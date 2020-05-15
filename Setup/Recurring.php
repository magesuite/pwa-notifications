<?php

namespace MageSuite\PwaNotifications\Setup;

class Recurring implements \Magento\Framework\Setup\InstallSchemaInterface
{
    /**
     * @var \MageSuite\PwaNotifications\Model\Permission\Pool
     */
    protected $permissionPool;

    public function __construct(
        \MageSuite\PwaNotifications\Model\Permission\Pool $permissionPool,
        \Magento\Framework\App\ResourceConnection $resourceConnection
    ) {
        $this->permissionPool = $permissionPool;
        $this->connection = $resourceConnection->getConnection();
    }

    /**
     *
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     * @return void
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(\Magento\Framework\Setup\SchemaSetupInterface $setup, \Magento\Framework\Setup\ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $tableName = $this->connection->getTableName('pwa_permission');

        foreach ($this->permissionPool->getPermissions() as $permissionIdentifier) {
            $this->connection->insertOnDuplicate($tableName, ['identifier' => $permissionIdentifier]);
        }

        $installer->endSetup();
    }
}
