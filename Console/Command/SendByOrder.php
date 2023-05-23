<?php

namespace MageSuite\PwaNotifications\Console\Command;

class SendByOrder extends \Symfony\Component\Console\Command\Command
{
    /**
     * @var \Magento\Framework\App\State
     */
    protected $state;

    /**
     * @var \MageSuite\PwaNotifications\Api\Data\NotificationInterfaceFactory
     */
    protected $notificationFactory;

    /**
     * @var \MageSuite\PwaNotifications\Model\Notification\SendByOrderFactory
     */
    protected $sendByOrderFactory;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterfaceFactory
     */
    protected $orderRepositoryFactory;

    public function __construct(
        \MageSuite\PwaNotifications\Model\Notification\SendByOrderFactory $sendByOrderFactory,
        \Magento\Sales\Api\OrderRepositoryInterfaceFactory $orderRepositoryFactory,
        \MageSuite\PwaNotifications\Api\Data\NotificationInterfaceFactory $notificationFactory,
        \Magento\Framework\App\State $state
    ) {
        parent::__construct();

        $this->sendByOrderFactory = $sendByOrderFactory;
        $this->orderRepositoryFactory = $orderRepositoryFactory;
        $this->state = $state;
        $this->notificationFactory = $notificationFactory;
    }

    protected function configure()
    {
        $this
            ->setName('pwa:notification:send_by_order')
            ->setDescription('Send notification based on order id')
            ->addArgument('orderId', \Symfony\Component\Console\Input\InputOption::VALUE_REQUIRED, 'Order Id')
            ->addArgument('body', \Symfony\Component\Console\Input\InputOption::VALUE_REQUIRED, 'body')
            ->addOption('permissions', 'p', \Symfony\Component\Console\Input\InputOption::VALUE_OPTIONAL, 'Comma delimited list of permissions needed');
    }

    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    ) {
        // phpcs:ignore
        $this->state->emulateAreaCode('frontend', function () use ($input, $output) {
            $orderId = $input->getArgument('orderId');
            $body = $input->getArgument('body');
            $permissions = $input->getOption('permissions') ? explode(',', $input->getOption('permissions')) : [];

            $notification = $this->notificationFactory->create();
            $notification->setBody($body);

            $order = $this->orderRepositoryFactory->create()->get($orderId);

            $this->sendByOrderFactory->create()->execute($order, $notification, $permissions);

            $output->writeln('Message was sent');
        });

        return \Magento\Framework\Console\Cli::RETURN_SUCCESS;
    }
}
