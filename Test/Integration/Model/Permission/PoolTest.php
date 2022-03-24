<?php

namespace MageSuite\PwaNotifications\Test\Integration\Model\Permission;

class PoolTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \MageSuite\PwaNotifications\Model\Permission\Pool
     */
    protected $permissionPool;

    protected function setUp(): void
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->permissionPool = $this->objectManager->get(\MageSuite\PwaNotifications\Model\Permission\Pool::class);
    }

    /**
     * @magentoAppArea frontend
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     */
    public function testItReturnsAllPermissions()
    {
        $permissions = $this->permissionPool->getPermissions();

        $this->assertArrayHasKey('order_status_notification', $permissions);
        $this->assertArrayHasKey('advertisement', $permissions);
    }

    /**
     * @magentoAppArea frontend
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoConfigFixture current_store pwa/permission/advertisement 0
     */
    public function testItDoesNotReturnAdvertisementPermission()
    {
        $permissions = $this->permissionPool->getPermissions();

        $this->assertArrayHasKey('order_status_notification', $permissions);
        $this->assertArrayNotHasKey('advertisement', $permissions);
    }
}
