<?php

namespace MageSuite\PwaNotifications\Model\Permission;

class Pool
{
    /**
     * @var \MageSuite\PwaNotifications\Helper\Configuration
     */
    protected $configuration;

    /**
     * @var string[]
     */
    protected $permissions;

    public function __construct(
        \MageSuite\PwaNotifications\Helper\Configuration $configuration,
        array $permissions = []
    ) {
        $this->configuration = $configuration;
        $this->permissions = $permissions;
    }

    public function getPermissions()
    {
        $permissions = [];

        foreach ($this->permissions as $permissionCode => $permission) {
            if (!$this->configuration->isPermissionAvailable($permissionCode)) {
                continue;
            }

            $permissions[$permissionCode] = $permission;
        }

        return $permissions;
    }
}
