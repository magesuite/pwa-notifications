<?php

namespace MageSuite\PwaNotifications\ViewModel;

class Permissions implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    /**
     * @var \MageSuite\PwaNotifications\Model\Device\GetRelatedDevices
     */
    protected $getRelatedDevices;

    /**
     * @var \MageSuite\PwaNotifications\Helper\Session
     */
    protected $sessionHelper;

    /**
     * @var \MageSuite\PwaNotifications\Model\Permission\Repository
     */
    protected $permissionRepository;

    /**
     * @var \MageSuite\PwaNotifications\Model\Permission\Pool
     */
    protected $permissionsPool;

    public function __construct(
        \MageSuite\PwaNotifications\Model\Device\GetRelatedDevices $getRelatedDevices,
        \MageSuite\PwaNotifications\Helper\Session $sessionHelper,
        \MageSuite\PwaNotifications\Model\Permission\Repository $permissionRepository,
        \MageSuite\PwaNotifications\Model\Permission\Pool $permissionsPool
    ) {
        $this->getRelatedDevices = $getRelatedDevices;
        $this->sessionHelper = $sessionHelper;
        $this->permissionRepository = $permissionRepository;
        $this->permissionsPool = $permissionsPool;
    }

    public function getCurrentDevicePermissions()
    {
        $deviceId = $this->sessionHelper->getDeviceId();

        if ($deviceId == null) {
            return [];
        }

        $devicesIds = array_merge([$deviceId], $this->getRelatedDevices->execute($deviceId));

        return $this->permissionRepository->getDevicesPermissions($devicesIds);
    }

    public function getPermissions()
    {
        return $this->permissionsPool->getPermissions();
    }
}
