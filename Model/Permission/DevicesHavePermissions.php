<?php

namespace MageSuite\PwaNotifications\Model\Permission;

class DevicesHavePermissions
{
    /**
     * @var Repository
     */
    protected $permissionRepository;

    public function __construct(\MageSuite\PwaNotifications\Model\Permission\Repository $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;
    }

    public function execute($devicesIds, $requiredPermissions)
    {
        if (empty($requiredPermissions)) {
            return true;
        }

        $devicesPermissions = $this->permissionRepository->getDevicesPermissions($devicesIds);

        if (empty($devicesPermissions)) {
            return false;
        }

        foreach ($requiredPermissions as $requiredPermission) {
            if (!in_array($requiredPermission, $devicesPermissions)) {
                return false;
            }
        }

        return true;
    }
}
