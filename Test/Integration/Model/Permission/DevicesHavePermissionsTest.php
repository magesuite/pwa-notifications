<?php

namespace MageSuite\PwaNotifications\Test\Integration\Model\Permission;

class DevicesHavePermissionsTest extends \PHPUnit\Framework\TestCase
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
     * @var \MageSuite\PwaNotifications\Model\Permission\DevicesHavePermissions
     */
    protected $devicesHavePermissions;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->deviceHelper = $this->objectManager->create(\MageSuite\PwaNotifications\Test\Integration\DeviceHelper::class);
        $this->devicesHavePermissions = $this->objectManager->create(\MageSuite\PwaNotifications\Model\Permission\DevicesHavePermissions::class);
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testItReturnsTrueWhenDeviceHasAllRequiredPermissions()
    {
        $deviceId = $this->deviceHelper->createDevice('test', ['order_status_notification']);

        $this->assertTrue($this->devicesHavePermissions->execute([$deviceId], ['order_status_notification']));
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testItReturnsFalseWhenDeviceHasOnlyPartOfRequiredPermissions()
    {
        $deviceId = $this->deviceHelper->createDevice('test', ['order_status_notification']);

        $this->assertFalse($this->devicesHavePermissions->execute([$deviceId], ['order_status_notification', 'advertisement']));
    }
}
