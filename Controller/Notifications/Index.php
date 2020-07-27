<?php
namespace MageSuite\PwaNotifications\Controller\Notifications;


class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Customer\Model\Session $customerSession
    )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->customerSession = $customerSession;
    }

    /**
     * Index action
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if(!$this->customerSession->getCustomerId()){
            $this->messageManager->addNoticeMessage(__('Your session expired, please login again.'));
            $this->_redirect('customer/account/login');
        }
        /** @var $resultPage \Magento\Framework\View\Result\Page */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Notifications'));

        return $resultPage;
    }
}