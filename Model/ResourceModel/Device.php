<?php

namespace MageSuite\PwaNotifications\Model\ResourceModel;

class Device extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @var \MageSuite\PwaNotifications\Model\Permission\Repository
     */
    protected $permissionRepository;

    /**
     * @var \MageSuite\PwaNotifications\Model\Device\GetRelatedDevices
     */
    protected $getRelatedDevices;

    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \MageSuite\PwaNotifications\Model\Permission\Repository $permissionRepository,
        \MageSuite\PwaNotifications\Model\Device\GetRelatedDevices $getRelatedDevices,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->permissionRepository = $permissionRepository;
        $this->getRelatedDevices = $getRelatedDevices;
    }

    protected function _construct()
    {
        $this->_init('pwa_device', 'device_id');
    }

    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object->getId()) {
            $devicesIds = array_merge([$object->getId()], $this->getRelatedDevices->execute($object->getId()));
            $permissions = $this->permissionRepository->getDevicesPermissions($devicesIds);

            $object->setPermissions($permissions);
        }

        return parent::_afterLoad($object);
    }

    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object->getPermissions()) {
            $devicesIds = array_merge([$object->getId()], $this->getRelatedDevices->execute($object->getId()));
            $oldPermissions = $this->permissionRepository->getDevicesPermissions($devicesIds);
            $newPermissions = (array)$object->getPermissions();

            $permissionsToDelete = array_diff($oldPermissions, $newPermissions);

            if ($permissionsToDelete) {
                $this->permissionRepository->removePermissions($devicesIds, $permissionsToDelete);
            }

            $permissionsToInsert = array_diff($newPermissions, $oldPermissions);

            if ($permissionsToInsert) {
                $this->permissionRepository->addPermissions($devicesIds, $permissionsToInsert);
            }
        }

        return parent::_afterSave($object);
    }
}
