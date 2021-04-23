<?php

namespace MageSuite\PwaNotifications\Test\Integration\Model\Notification;

class SendByCustomerTest extends AbstractNotificationTest
{
    /**
     * @var \MageSuite\PwaNotifications\Model\EmailToDeviceRepository
     */
    protected $emailToDeviceRepository;

    /**
     * @var \MageSuite\PwaNotifications\Model\CustomerToDeviceRepository
     */
    protected $customerToDeviceRepository;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \MageSuite\PwaNotifications\Model\Notification\SendByCustomer
     */
    protected $sendByCustomer;

    /**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    protected $sessionManager;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->emailToDeviceRepository = $this->objectManager->create(\MageSuite\PwaNotifications\Model\EmailToDeviceRepository::class);
        $this->customerToDeviceRepository = $this->objectManager->create(\MageSuite\PwaNotifications\Model\CustomerToDeviceRepository::class);
        $this->customerRepository = $this->objectManager->create(\Magento\Customer\Api\CustomerRepositoryInterface::class);
        $this->sendByCustomer = $this->objectManager->create(\MageSuite\PwaNotifications\Model\Notification\SendByCustomer::class);
        $this->sessionManager = $this->objectManager->create(\Magento\Framework\Session\SessionManagerInterface::class);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDbIsolation enabled
     */
    public function testItPublishesMessagesToQueue()
    {
        $firstDeviceId = $this->deviceHelper->createDevice('firstEndpointTest');
        $this->sessionManager->setPwaDeviceId(0);

        $secondDeviceId = $this->deviceHelper->createDevice('secondEndpointTest');

        $customer = $this->customerRepository->get('customer@example.com');

        $this->emailToDeviceRepository->save($customer->getEmail(), $firstDeviceId);
        $this->customerToDeviceRepository->save($customer->getId(), $secondDeviceId);

        $notification = $this->notificationFactory->create();
        $notification->setBody('test message');

        $this->sendByCustomer->execute($customer, $notification);

        $messages = $this->getMessages();

        $this->assertCount(2, $messages);
        $this->assertEquals($secondDeviceId, $messages[0]['deviceId']);
        $this->assertEquals($firstDeviceId, $messages[1]['deviceId']);
        $this->assertEquals('test message', $messages[0]['body']);
        $this->assertEquals('test message', $messages[1]['body']);
    }
}
