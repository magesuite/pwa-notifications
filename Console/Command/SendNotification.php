<?php

namespace MageSuite\PwaNotifications\Console\Command;

class SendNotification extends \Symfony\Component\Console\Command\Command
{
    /**
     * @var \MageSuite\PwaNotifications\Model\Notification\PublishToQueue
     */
    protected $publishToQueue;

    /**
     * @var \MageSuite\PwaNotifications\Api\Data\NotificationInterfaceFactory
     */
    protected $notificationFactory;

    public function __construct(
        \MageSuite\PwaNotifications\Model\Notification\PublishToQueue $publishToQueue,
        \MageSuite\PwaNotifications\Api\Data\NotificationInterfaceFactory $notificationFactory
    ) {
        parent::__construct();

        $this->publishToQueue = $publishToQueue;
        $this->notificationFactory = $notificationFactory;
    }

    protected function configure()
    {
        $this
            ->setName('pwa:notification:send')
            ->setDescription('Send notification to device')
            ->addArgument('deviceId', \Symfony\Component\Console\Input\InputOption::VALUE_REQUIRED, 'Device ID')
            ->addArgument('body', \Symfony\Component\Console\Input\InputOption::VALUE_REQUIRED, 'Body')
            ->addOption('title', null, \Symfony\Component\Console\Input\InputOption::VALUE_OPTIONAL, 'Title', null)
            ->addOption('url', null, \Symfony\Component\Console\Input\InputOption::VALUE_OPTIONAL, 'Url', null)
            ->addOption('image', null, \Symfony\Component\Console\Input\InputOption::VALUE_OPTIONAL, 'Image URL', null)
            ->addOption('icon', null, \Symfony\Component\Console\Input\InputOption::VALUE_OPTIONAL, 'Icon URL', null)
            ->addOption('badge', null, \Symfony\Component\Console\Input\InputOption::VALUE_OPTIONAL, 'Badge URL', null)
        ;
    }

    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    ) {
        $deviceId = $input->getArgument('deviceId');
        $body = $input->getArgument('body');

        /** @var \MageSuite\PwaNotifications\Api\Data\NotificationInterface $notification */
        $notification = $this->notificationFactory->create();
        $notification->setBody($body);

        if ($input->getOption('title')) {
            $notification->setTitle($input->getOption('title'));
        }

        if ($input->getOption('url')) {
            $notification->setUrl($input->getOption('url'));
        }

        if ($input->getOption('image')) {
            $notification->setImage($input->getOption('image'));
        }

        if ($input->getOption('icon')) {
            $notification->setIcon($input->getOption('icon'));
        }

        if ($input->getOption('badge')) {
            $notification->setBadge($input->getOption('badge'));
        }

        $this->publishToQueue->execute($deviceId, $notification);

        $output->writeln('Message was published to queue');
    }
}
