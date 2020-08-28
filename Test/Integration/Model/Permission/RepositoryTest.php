<?php

namespace MageSuite\PwaNotifications\Test\Integration\Model\Permission;

class RepositoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \MageSuite\PwaNotifications\Test\Integration\DeviceHelper
     */
    protected $deviceHelper;

    /**
     * @var \MageSuite\PwaNotifications\Model\Permission\Repository
     */
    protected $permissionRepository;

    /**
     * @var \MageSuite\PwaNotifications\Model\Permission\DevicesHavePermissions
     */
    protected $devicesHavePermissions;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->deviceHelper = $this->objectManager->create(\MageSuite\PwaNotifications\Test\Integration\DeviceHelper::class);
        $this->devicesHavePermissions = $this->objectManager->create(\MageSuite\PwaNotifications\Model\Permission\DevicesHavePermissions::class);
        $this->permissionRepository = $this->objectManager->create(\MageSuite\PwaNotifications\Model\Permission\Repository::class);
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testItRemovesPermissions()
    {
        $deviceId = $this->deviceHelper->createDevice('test', ['order_status_notification', 'advertisement']);

        $this->permissionRepository->removePermissions([$deviceId], ['advertisement']);

        $this->assertTrue($this->devicesHavePermissions->execute([$deviceId], ['order_status_notification']));
        $this->assertFalse($this->devicesHavePermissions->execute([$deviceId], ['advertisement']));
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testItAddsPermission()
    {
        $deviceId = $this->deviceHelper->createDevice('test', ['order_status_notification']);

        $this->permissionRepository->addPermissions([$deviceId], ['advertisement']);

        $this->assertTrue(
            $this->devicesHavePermissions->execute(
                [$deviceId],
                ['order_status_notification', 'advertisement']
            )
        );
    }
}
