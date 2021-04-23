<?php

namespace MageSuite\PwaNotifications\Test\Integration\Model\Notification;

abstract class AbstractNotificationTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \MageSuite\PwaNotifications\Test\Integration\DeviceHelper
     */
    protected $deviceHelper;

    /**
     * @var \Magento\MysqlMq\Model\ResourceModel\MessageCollectionFactory
     */
    protected $messageCollectionFactory;

    /**
     * @var \MageSuite\PwaNotifications\Api\Data\NotificationInterfaceFactory
     */
    protected $notificationFactory;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->deviceHelper = $this->objectManager->create(\MageSuite\PwaNotifications\Test\Integration\DeviceHelper::class);
        $this->messageCollectionFactory = $this->objectManager->create(\Magento\MysqlMq\Model\ResourceModel\MessageCollectionFactory::class);
        $this->notificationFactory = $this->objectManager->create(\MageSuite\PwaNotifications\Api\Data\NotificationInterfaceFactory::class);
    }

    protected function getMessages()
    {
        $messageCollection = $this->messageCollectionFactory->create();
        $messageCollection->addFieldToFilter('topic_name', \MageSuite\Queue\Service\Publisher::DATABASE_CONSUMER_NAME);

        $messages = array_map([$this, 'decodeData'], $messageCollection->getColumnValues('body'));
        return $messages;
    }

    protected function decodeData($messageBody)
    {
        $decodedBody = json_decode($messageBody, true);
        $data = json_decode($decodedBody['data']);

        return json_decode($data, true);
    }
}
