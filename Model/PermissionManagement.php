<?php

namespace MageSuite\PwaNotifications\Model;

class PermissionManagement implements \MageSuite\PwaNotifications\Api\PermissionManagementInterface
{
    /**
     * @var Device\GetRelatedDevices
     */
    protected $getRelatedDevices;

    /**
     * @var \MageSuite\PwaNotifications\Helper\Session
     */
    protected $session;

    /**
     * @var Permission\Repository
     */
    protected $permissionRepository;

    public function __construct(
        \MageSuite\PwaNotifications\Model\Device\GetRelatedDevices $getRelatedDevices,
        \MageSuite\PwaNotifications\Helper\Session $session,
        \MageSuite\PwaNotifications\Model\Permission\Repository $permissionRepository
    ) {
        $this->getRelatedDevices = $getRelatedDevices;
        $this->session = $session;
        $this->permissionRepository = $permissionRepository;
    }

    /**
     * @inheritDoc
     */
    public function add($permission)
    {
        $deviceId = $this->session->getDeviceId();

        if (!is_numeric($deviceId)) {
            return false;
        }

        $devicesIds = array_merge([$deviceId], $this->getRelatedDevices->execute($deviceId));

        $this->permissionRepository->addPermissions($devicesIds, [$permission]);

        return true;
    }

    /**
     * @inheritDoc
     */
    public function remove($permission)
    {
        $deviceId = $this->session->getDeviceId();

        if (!is_numeric($deviceId)) {
            return false;
        }

        $devicesIds = array_merge([$deviceId], $this->getRelatedDevices->execute($deviceId));

        $this->permissionRepository->removePermissions($devicesIds, [$permission]);

        return true;
    }

    /**
     * @inheritDoc
     */
    public function get()
    {
        $deviceId = $this->session->getDeviceId();

        if ($deviceId == null) {
            return [];
        }

        $devicesIds = array_merge([$deviceId], $this->getRelatedDevices->execute($deviceId));

        $permissions = $this->permissionRepository->getDevicesPermissions($devicesIds);

        if (empty($permissions)) {
            return [];
        }

        return $permissions;
    }
}
