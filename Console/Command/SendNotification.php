<?php

namespace MageSuite\PwaNotifications\Console\Command;

class SendNotification extends \Symfony\Component\Console\Command\Command
{
    /**
     * @var \MageSuite\PwaNotifications\Model\Notification\PublishToQueue
     */
    protected $publishToQueue;

    public function __construct(\MageSuite\PwaNotifications\Model\Notification\PublishToQueue $publishToQueue)
    {
        parent::__construct();

        $this->publishToQueue = $publishToQueue;
    }

    protected function configure()
    {
        $this
            ->setName('pwa:notification:send')
            ->setDescription('Send notification to device')
            ->addArgument('deviceId', \Symfony\Component\Console\Input\InputOption::VALUE_REQUIRED, 'Device ID')
            ->addArgument('message', \Symfony\Component\Console\Input\InputOption::VALUE_REQUIRED, 'Message');
    }

    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    )
    {
        $deviceId = $input->getArgument('deviceId');
        $message = $input->getArgument('message');

        $this->publishToQueue->execute($deviceId, $message);

        $output->writeln('Message was published to queue');
    }
}

