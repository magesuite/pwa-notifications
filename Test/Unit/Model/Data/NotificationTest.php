<?php

namespace MageSuite\PwaNotifications\Test\Unit\Model\Data;

class NotificationTest extends \PHPUnit\Framework\TestCase
{
    public function testItCorrectlyMapsFieldsToJson()
    {
        $notification = new \MageSuite\PwaNotifications\Model\Data\Notification();

        $notification->setTitle('test_title');
        $notification->setBody('test_body');
        $notification->setImage('test_image');
        $notification->setIcon('test_icon');
        $notification->setUrl('test_url');
        $notification->setBadge('test_badge');

        $notificationJson = (string)$notification;
        $notificationArray = json_decode($notificationJson, true);

        $this->assertFalse(isset($notificationArray['device_id']));
        $this->assertEquals('test_title', $notificationArray['title']);
        $this->assertEquals('test_body', $notificationArray['body']);
        $this->assertEquals('test_image', $notificationArray['image']);
        $this->assertEquals('test_icon', $notificationArray['icon']);
        $this->assertEquals('test_url', $notificationArray['data']['url']);
        $this->assertEquals('test_badge', $notificationArray['badge']);
    }
}
