<?php

namespace MageSuite\PwaNotifications\Model\Device;

class GetRelatedDevices
{
    /**
     * @var \MageSuite\PwaNotifications\Model\DeviceInformationManagement
     */
    protected $deviceInformationManagement;

    /**
     * @var \MageSuite\PwaNotifications\Model\EmailToDeviceRepository
     */
    protected $emailToDeviceRepository;

    /**
     * @var \MageSuite\PwaNotifications\Model\OrderToDeviceRepository
     */
    protected $orderToDeviceRepository;

    /**
     * @var \MageSuite\PwaNotifications\Model\CustomerToDeviceRepository
     */
    protected $customerToDeviceRepository;

    public function __construct(
        \MageSuite\PwaNotifications\Model\DeviceInformationManagement $deviceInformationManagement,
        \MageSuite\PwaNotifications\Model\EmailToDeviceRepository $emailToDeviceRepository,
        \MageSuite\PwaNotifications\Model\OrderToDeviceRepository $orderToDeviceRepository,
        \MageSuite\PwaNotifications\Model\CustomerToDeviceRepository $customerToDeviceRepository
    ) {
        $this->deviceInformationManagement = $deviceInformationManagement;
        $this->emailToDeviceRepository = $emailToDeviceRepository;
        $this->orderToDeviceRepository = $orderToDeviceRepository;
        $this->customerToDeviceRepository = $customerToDeviceRepository;
    }

    public function execute($deviceId)
    {
        $relatedDevicesIds = [];

        $relatedDevicesIds = array_merge($relatedDevicesIds, $this->emailToDeviceRepository->getRelatedDevices($deviceId));
        $relatedDevicesIds = array_merge($relatedDevicesIds, $this->orderToDeviceRepository->getRelatedDevices($deviceId));
        $relatedDevicesIds = array_merge($relatedDevicesIds, $this->customerToDeviceRepository->getRelatedDevices($deviceId));

        return array_unique($relatedDevicesIds);
    }
}
