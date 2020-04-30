<?php

namespace MageSuite\PwaNotifications\Model\Notification;

class SendByEmail
{
    /**
     * @var PublishToQueue
     */
    protected $publishToQueue;

    /**
     * @var \MageSuite\PwaNotifications\Model\EmailToDeviceRepository
     */
    protected $emailToDeviceRepository;

    public function __construct(
        PublishToQueue $publishToQueue,
        \MageSuite\PwaNotifications\Model\EmailToDeviceRepository $emailToDeviceRepository
    ) {
        $this->publishToQueue = $publishToQueue;
        $this->emailToDeviceRepository = $emailToDeviceRepository;
    }

    /**
     * @param string $email
     * @param $message
     */
    public function execute($email, $message)
    {
        $deviceIds = $this->emailToDeviceRepository->getDevicesByEmail($email);

        if (empty($deviceIds)) {
            return;
        }

        foreach ($deviceIds as $deviceId) {
            $this->publishToQueue->execute($deviceId, $message);
        }
    }
}
