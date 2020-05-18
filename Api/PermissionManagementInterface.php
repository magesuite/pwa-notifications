<?php

namespace MageSuite\PwaNotifications\Api;

interface PermissionManagementInterface
{
    /**
     * @param string $permission
     * @return mixed
     */
    public function add($permission);

    /**
     * @param string $permission
     * @return mixed
     */
    public function remove($permission);
}
