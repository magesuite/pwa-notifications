<?php

namespace MageSuite\PwaNotifications\Model\Notification;

class SendByCustomer
{
    /**
     * @var PublishToQueue
     */
    protected $publishToQueue;

    /**
     * @var \MageSuite\PwaNotifications\Model\EmailToDeviceRepository
     */
    protected $emailToDeviceRepository;

    /**
     * @var \MageSuite\PwaNotifications\Model\CustomerToDeviceRepository
     */
    protected $customerToDeviceRepository;

    public function __construct(
        PublishToQueue $publishToQueue,
        \MageSuite\PwaNotifications\Model\EmailToDeviceRepository $emailToDeviceRepository,
        \MageSuite\PwaNotifications\Model\CustomerToDeviceRepository $customerToDeviceRepository
    ) {
        $this->publishToQueue = $publishToQueue;
        $this->emailToDeviceRepository = $emailToDeviceRepository;
        $this->customerToDeviceRepository = $customerToDeviceRepository;
    }

    /**
     * @param \Magento\Customer\Model\Customer $customer
     * @param $message
     */
    public function execute($customer, $message)
    {
        $customerId = $customer->getId();

        $deviceIds = $this->customerToDeviceRepository->getDevicesByCustomerId($customerId);
        $deviceIds = array_merge($deviceIds, $this->emailToDeviceRepository->getDevicesByEmail($customer->getEmail()));

        $deviceIds = array_unique($deviceIds);

        if (empty($deviceIds)) {
            return;
        }

        foreach ($deviceIds as $deviceId) {
            $this->publishToQueue->execute($deviceId, $message);
        }
    }
}
