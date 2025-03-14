<?php
namespace Vendor\PrivacyPolicy\Block;

use Magento\Framework\View\Element\Template;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Customer\Api\CustomerRepositoryInterface;

class PrivacyModal extends Template
{
    protected $customerSession;
    protected $customerRepository;
    
    public function __construct(
        Template\Context $context,
        CustomerSession $customerSession,
        CustomerRepositoryInterface $customerRepository,
        array $data = []
    ) {
        $this->customerSession    = $customerSession;
        $this->customerRepository = $customerRepository;
        parent::__construct($context, $data);
    }
    
    public function shouldShowModal()
    {
        $customerId = $this->customerSession->getCustomerId();
        if (!$customerId) {
            return false;
        }
        
        $customerData = $this->customerRepository->getById($customerId);
        $accepted = $customerData->getCustomAttribute('privacy_policy_accepted')
            ? $customerData->getCustomAttribute('privacy_policy_accepted')->getValue()
            : 0;
        
        return !$accepted;
    }
}
