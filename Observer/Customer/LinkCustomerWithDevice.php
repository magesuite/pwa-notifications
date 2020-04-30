<?php

namespace MageSuite\PwaNotifications\Observer\Customer;

class LinkCustomerWithDevice implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \MageSuite\PwaNotifications\Helper\Session
     */
    protected $session;

    /**
     * @var \MageSuite\PwaNotifications\Model\CustomerToDeviceRepository
     */
    protected $customerToDeviceRepository;

    /**
     * @var \MageSuite\PwaNotifications\Model\EmailToDeviceRepository
     */
    protected $emailToDeviceRepository;

    public function __construct(
        \MageSuite\PwaNotifications\Helper\Session $session,
        \MageSuite\PwaNotifications\Model\CustomerToDeviceRepository $customerToDeviceRepository,
        \MageSuite\PwaNotifications\Model\EmailToDeviceRepository $emailToDeviceRepository
    ) {
        $this->session = $session;
        $this->customerToDeviceRepository = $customerToDeviceRepository;
        $this->emailToDeviceRepository = $emailToDeviceRepository;
    }

    /**
     * @inheritDoc
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->session->getDeviceId()) {
            return;
        }

        /** @var \Magento\Customer\Model\Customer $customer */
        $customer = $observer->getEvent()->getCustomer();

        if ($customer == null) {
            return;
        }

        $deviceId = $this->session->getDeviceId();

        $this->customerToDeviceRepository->save($customer->getId(), $deviceId);
        $this->emailToDeviceRepository->save($customer->getEmail(), $deviceId);
    }
}
