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
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->deviceHelper = $this->objectManager->create(\MageSuite\PwaNotifications\Test\Integration\DeviceHelper::class);
        $this->messageCollectionFactory = $this->objectManager->create(\Magento\MysqlMq\Model\ResourceModel\MessageCollectionFactory::class);
    }

    protected function getMessages() {
        $messageCollection = $this->messageCollectionFactory->create();
        $messageCollection->addFieldToFilter('topic_name', 'pwa.notification.send');

        $messages = array_map('json_decode', $messageCollection->getColumnValues('body'));

        return $messages;
    }
}
