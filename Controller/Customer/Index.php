<?php

namespace MageSuite\PwaNotifications\Controller\Customer;

class Index extends \MageSuite\PwaNotificationsController\Customer
{
    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_PAGE);
        if ($navigationBlock = $resultPage->getLayout()->getBlock('customer_account_navigation')) {
            $navigationBlock->setActive('notifications/customer');
        }
        $resultPage->getConfig()->getTitle()->set(__('Notifications'));
        return $resultPage;
    }
}