<?php

namespace MageSuite\PwaNotifications\Api;

interface PermissionManagementInterface
{
    /**
     * @param string $permission
     * @return bool
     */
    public function add($permission);

    /**
     * @param string $permission
     * @return bool
     */
    public function remove($permission);

    /**
     * @return string[]
     */
    public function get();
}
