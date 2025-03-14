<?php
namespace Vendor\PrivacyPolicy\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Customer\Model\Session as CustomerSession;

class CustomerLoginObserver implements ObserverInterface
{
    protected $customerSession;
    
    public function __construct(CustomerSession $customerSession)
    {
        $this->customerSession = $customerSession;
    }
    
    public function execute(Observer $observer)
    {
        $customer = $observer->getEvent()->getCustomer();
        $accepted = $customer->getCustomAttribute('privacy_policy_accepted')
            ? $customer->getCustomAttribute('privacy_policy_accepted')->getValue()
            : 0;
        if (!$accepted) {
            $this->customerSession->setShowPrivacyModal(true);
        }
        return $this;
    }
}
