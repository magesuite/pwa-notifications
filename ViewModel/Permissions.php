<?php

namespace MageSuite\PwaNotifications\ViewModel;

class Permissions
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

    public function __construct(
        \MageSuite\PwaNotifications\Model\Device\GetRelatedDevices $getRelatedDevices,
        \MageSuite\PwaNotifications\Helper\Session $sessionHelper,
        \MageSuite\PwaNotifications\Model\Permission\Repository $permissionRepository
    ) {
        $this->getRelatedDevices = $getRelatedDevices;
        $this->sessionHelper = $sessionHelper;
        $this->permissionRepository = $permissionRepository;
    }

    public function getCurrentPermissions()
    {
        $deviceId = $this->sessionHelper->getDeviceId();

        if ($deviceId == null) {
            return [];
        }

        $devicesIds = array_merge([$deviceId], $this->getRelatedDevices->execute($deviceId));

        return $this->permissionRepository->getDevicesPermissions($devicesIds);
    }
}
