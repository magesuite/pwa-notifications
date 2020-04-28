<?php

namespace MageSuite\PwaNotifications\Model\Notification;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\TemporaryStateExceptionInterface;
use Magento\Framework\Bulk\OperationInterface;

class Consumer
{
    const ERROR_MESSAGE = 'Sorry, something went wrong during PWA notifications sending process. Please see log for details.';

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    protected $serializer;

    /**
     * @var \Magento\Framework\Bulk\OperationManagementInterface
     */
    protected $operationManagement;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @param \Magento\Framework\Bulk\OperationManagementInterface $operationManagement
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Serialize\SerializerInterface $serializer
     * @param EntityManager $entityManager
     */
    public function __construct(
        \Magento\Framework\Bulk\OperationManagementInterface $operationManagement,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        EntityManager $entityManager
    ) {
        $this->logger = $logger;
        $this->serializer = $serializer;
        $this->operationManagement = $operationManagement;
        $this->entityManager = $entityManager;
    }

    public function process(\MageSuite\PwaNotifications\Api\Data\NotificatioInterface $notification) {
        $this->logger->info('Notification sent '.$notification);
    }
}
