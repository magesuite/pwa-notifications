<?php

namespace MageSuite\PwaNotifications\Model\Permission;

class Pool
{
    /**
     * @var string[]
     */
    protected $permissions;

    public function __construct(array $permissions = [])
    {
        $this->permissions = $permissions;
    }

    public function getPermissions()
    {
        return $this->permissions;
    }
}
